--app and token

----------
--tables--
----------

CREATE TABLE public.apps (
  app_id TEXT PRIMARY KEY,
  app_name TEXT NOT NULL UNIQUE,
  descr TEXT,
  app_secret TEXT NOT NULL,
  redirect_uri TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
);

CREATE TABLE public.access_tokens (
  id SERIAL PRIMARY KEY,
  user_id TEXT NOT NULL REFERENCES public.users (user_id),
  app_id TEXT REFERENCES public.apps (app_id),
  access_token TEXT NOT NULL UNIQUE,
  expires_in TIMESTAMP WITH TIME ZONE NOT NULL,
  scope TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
);

-------------
--functions--
-------------

CREATE OR REPLACE FUNCTION public.verify_token(in_token TEXT, in_app_id TEXT)
  RETURNS BOOLEAN AS $BODY$
DECLARE
  _r RECORD;
  _s TEXT;
  _token TEXT;
  _exp TIMESTAMP WITH TIME ZONE;
BEGIN
  IF in_app_id IS NULL THEN
    SELECT user_id, expires_in, scope INTO _r FROM public.access_tokens
    WHERE app_id IS NULL AND access_token = in_token;
  ELSE
    SELECT user_id, expires_in, scope INTO _r FROM public.access_tokens
    WHERE app_id = in_app_id AND access_token = in_token;
  END IF;
  IF FOUND THEN
    IF _r.expires_in >= current_timestamp THEN
      RETURN TRUE;
    END IF;
  END IF;
  RETURN FALSE;
END;
$BODY$ LANGUAGE plpgsql
SECURITY DEFINER;

------------
--triggers--
------------

CREATE OR REPLACE FUNCTION public.tp_change_app() RETURNS TRIGGER AS $BODY$
DECLARE
  _id TEXT;
  _secret TEXT;
  _s BIGINT;
BEGIN
  CASE TG_OP
    WHEN 'INSERT' THEN
      _id := md5(NEW.app_name || NEW.descr || NEW.redirect_uri);
      NEW.app_id := _id;
      _secret := crypt(NEW.app_name || ':' || NEW.redirect_uri || ':' || current_timestamp::TEXT, gen_salt('bf'));
      NEW.app_secret := _secret;
      NEW.created_at = now();
      NEW.updated_at = now();
      RETURN NEW;
    WHEN 'UPDATE' THEN
      NEW.app_id = OLD.app_id;
      IF NEW.app_secret != OLD.app_secret THEN
        _secret := crypt(NEW.app_name || ':' || NEW.redirect_uri || ':' || current_timestamp::TEXT, gen_salt('bf'));
        NEW.app_secret := _secret;
      END IF;
      NEW.updated_at = now();
      RETURN NEW;
    WHEN 'DELETE' THEN
      DELETE FROM public.access_tokens WHERE app_id = OLD.app_id;
      RETURN OLD;
  END CASE;
END;
$BODY$ LANGUAGE plpgsql
SECURITY DEFINER;

CREATE TRIGGER tg_app BEFORE INSERT OR UPDATE OR DELETE ON public.apps
FOR EACH ROW EXECUTE PROCEDURE public.tp_change_app();

CREATE OR REPLACE FUNCTION public.tp_change_access_token() RETURNS TRIGGER AS $BODY$
DECLARE
  _exp TIMESTAMP WITH TIME ZONE;
  _token TEXT;
  _id TEXT;
  _secret TEXT;
  _s BIGINT;
BEGIN
  CASE TG_OP
    WHEN 'INSERT' THEN
      DELETE FROM public.access_tokens WHERE user_id = NEW.user_id AND app_id = NEW.app_id AND expires_in < current_timestamp;
      IF NEW.app_id IS NULL THEN
        _token := crypt(NEW.user_id || ':' || current_timestamp::TEXT, gen_salt('bf'));
      ELSE
        _token := crypt(NEW.user_id || ':' || NEW.app_id || ':' || current_timestamp::TEXT, gen_salt('bf'));
      END IF;
      NEW.access_token := _token;
      NEW.created_at = now();
      RETURN NEW;
    WHEN 'UPDATE' THEN
      RETURN NULL;
  END CASE;
END;
$BODY$ LANGUAGE plpgsql
SECURITY DEFINER;

CREATE TRIGGER tg_access_token BEFORE INSERT OR UPDATE ON public.access_tokens
FOR EACH ROW EXECUTE PROCEDURE public.tp_change_access_token();

---------
--views--
---------

CREATE OR REPLACE VIEW public.view_app_tokens AS
  SELECT apps.app_name, apps.descr, access_tokens.user_id, users.name AS user_name,
    access_tokens.expires_in, access_tokens.scope, access_tokens.created_at
  FROM public.access_tokens LEFT JOIN public.apps ON access_tokens.app_id = apps.app_id
    JOIN users ON access_tokens.user_id = users.user_id;
