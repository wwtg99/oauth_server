--Common user database
--version 0.1.0
--use extension pgcrypto

--------------
--department--
--------------

CREATE OR REPLACE RULE rule_department_delete AS ON DELETE TO public.departments
DO INSTEAD UPDATE public.departments SET deleted_at = now() WHERE department_id = OLD.department_id;

--------
--user--
--------

CREATE OR REPLACE FUNCTION public.get_user_id(in_user TEXT)
  RETURNS TEXT AS $BODY$
DECLARE
  _uid TEXT;
BEGIN
  SELECT user_id INTO _uid FROM public.users WHERE name = in_user AND deleted_at IS NULL;
  IF NOT FOUND THEN
    RAISE EXCEPTION 'There is no user % found!', in_user;
  ELSE
    RETURN _uid;
  END IF;
END;
$BODY$ LANGUAGE plpgsql
SECURITY DEFINER;

CREATE OR REPLACE FUNCTION public.create_user(in_name TEXT, in_dep TEXT, in_label TEXT, in_email TEXT DEFAULT NULL,
  in_descr TEXT DEFAULT NULL, in_superuser BOOLEAN DEFAULT FALSE)
  RETURNS TEXT AS $BODY$
DECLARE
  _id TEXT;
  _s BIGINT;
BEGIN
  SELECT user_id INTO _id FROM public.users WHERE name = in_name AND deleted_at IS NOT NULL;
  IF FOUND THEN
    UPDATE public.users SET deleted_at = NULL WHERE user_id = _id;
  ELSE
    _s := nextval('user_id_seq');
    _id := 'U' || lpad(_s::TEXT, 6, '0');
    INSERT INTO public.users (user_id, name, email, label, department_id, descr, superuser)
    VALUES (_id, in_name, in_email, in_label, in_dep, in_descr, in_superuser);
  END IF;
  INSERT INTO public.user_log (user_id, log_event) VALUES (_id, 'create');
  RETURN _id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.delete_user(in_user_id TEXT)
  RETURNS TEXT AS $BODY$
DECLARE
  _id TEXT;
BEGIN
  SELECT user_id INTO _id FROM public.users WHERE user_id = in_user_id AND deleted_at IS NULL;
  IF FOUND THEN
    UPDATE public.users SET deleted_at = now() WHERE user_id = in_user_id;
    INSERT INTO public.user_log (user_id, log_event) VALUES (_id, 'delete');
  END IF;
  RETURN in_user_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.update_user(in_user_id TEXT, in_user JSON)
  RETURNS TEXT AS $BODY$
DECLARE
  _id TEXT;
BEGIN
  SELECT user_id INTO _id FROM public.users WHERE user_id = in_user_id AND deleted_at IS NULL;
  IF FOUND THEN
    UPDATE public.users SET
      name = (in_user->>'name'),
      label = (in_user->>'label'),
      email = (in_user->>'email'),
      descr = (in_user->>'descr'),
      department_id = (in_user->>'department_id'),
      updated_at = now()
    WHERE user_id = in_user_id;
    INSERT INTO public.user_log (user_id, log_event, descr)
    VALUES (_id, 'update', in_user::TEXT);
  END IF;
  RETURN in_user_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE RULE rule_user_delete AS ON DELETE TO public.users
DO INSTEAD SELECT delete_user(OLD.user_id);

--------
--role--
--------

CREATE OR REPLACE FUNCTION public.get_role_id(in_role TEXT)
  RETURNS BIGINT AS $BODY$
DECLARE
  _rid BIGINT;
BEGIN
  SELECT role_id INTO _rid FROM public.roles WHERE name = in_role AND deleted_at IS NULL;
  IF NOT FOUND THEN
    RAISE EXCEPTION 'There is no role % found!', in_role;
  ELSE
    RETURN _rid;
  END IF;
END;
$BODY$ LANGUAGE plpgsql
SECURITY DEFINER;

CREATE OR REPLACE FUNCTION public.create_role(in_name TEXT, in_descr TEXT DEFAULT NULL)
  RETURNS BIGINT AS $BODY$
DECLARE
  _id BIGINT;
BEGIN
  SELECT role_id INTO _id FROM public.roles WHERE name = in_name AND deleted_at IS NOT NULL;
  IF FOUND THEN
    UPDATE public.roles SET deleted_at = NULL WHERE role_id = _id;
    RETURN _id;
  END IF;
  INSERT INTO public.roles (name, descr) VALUES (in_name, in_descr) RETURNING role_id INTO _id;
  RETURN _id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.delete_role(in_role_id BIGINT)
  RETURNS BIGINT AS $BODY$
DECLARE
  _id BIGINT;
BEGIN
  SELECT role_id INTO _id FROM public.roles WHERE role_id = in_role_id AND deleted_at IS NULL;
  IF FOUND THEN
    UPDATE public.roles SET deleted_at = now() WHERE role_id = in_role_id;
  END IF;
  RETURN in_role_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.update_role(in_role_id BIGINT, in_role JSON)
  RETURNS BIGINT AS $BODY$
DECLARE
  _id BIGINT;
BEGIN
  SELECT role_id INTO _id FROM public.roles WHERE role_id = in_role_id AND deleted_at IS NULL;
  IF FOUND THEN
    UPDATE public.roles SET
      name = (in_role->>'name'),
      descr = (in_role->>'descr'),
      updated_at = now()
    WHERE role_id = in_role_id;
  END IF;
  RETURN in_role_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE RULE rule_role_delete AS ON DELETE TO public.roles
DO INSTEAD SELECT delete_role(OLD.role_id);

-------------
--user role--
-------------

CREATE OR REPLACE FUNCTION public.add_role(in_user_id TEXT, in_role_id BIGINT)
  RETURNS BOOLEAN AS $BODY$
DECLARE
  _id BIGINT;
BEGIN
  --check exist
  SELECT user_id INTO _id FROM public.user_role
  WHERE user_id = in_user_id AND role_id = in_role_id;
  IF FOUND THEN
    RETURN TRUE;
  END IF;
  --add
  INSERT INTO public.user_role (user_id, role_id) VALUES (in_user_id, in_role_id);
  INSERT INTO public.user_log (user_id, log_event, descr)
  VALUES (in_user_id, 'add role', in_role_id::TEXT);
  RETURN TRUE;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.remove_role(in_user_id TEXT, in_role_id BIGINT)
  RETURNS BOOLEAN AS $BODY$
DECLARE
  _id BIGINT;
BEGIN
  SELECT user_id INTO _id FROM public.user_role
  WHERE user_id = in_user_id AND role_id = in_role_id;
  IF FOUND THEN
    DELETE FROM public.user_role WHERE user_id = in_user_id AND role_id = in_role_id;
    INSERT INTO public.user_log (user_id, log_event, descr)
    VALUES (in_user_id, 'remove role', in_role_id::TEXT);
    RETURN TRUE;
  END IF;
  RETURN FALSE;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.change_roles(in_user_id TEXT, in_roles JSON)
  RETURNS BOOLEAN AS $BODY$
DECLARE
  _id TEXT;
  _j JSON;
  _rid BIGINT;
  _rname TEXT;
  _rids BIGINT[];
  _b BOOLEAN;
BEGIN
  --check user
  SELECT user_id INTO _id FROM users WHERE user_id = in_user_id;
  IF NOT FOUND THEN
    RETURN FALSE;
  END IF;
  --get role id
  FOR _j IN SELECT json_array_elements(in_roles) LOOP
    _rid := _j->>'role_id';
    IF _rid IS NULL THEN
      _rname := _j->>'role_name';
      IF _rname IS NULL THEN
        CONTINUE;
      ELSE
        SELECT role_id INTO _rid FROM public.roles WHERE name = _rname;
      END IF;
    END IF;
    IF _rid IS NOT NULL THEN
      _rids := array_append(_rids, _rid);
    END IF;
  END LOOP;
  --delete roles
  DELETE FROM public.user_role WHERE user_id = in_user_id;
  --add roles
  INSERT INTO public.user_log (user_id, log_event) VALUES (in_user_id, 'clear roles');
  FOREACH _rid IN ARRAY _rids LOOP
    _b := add_role(in_user_id, _rid);
  END LOOP;
  RETURN TRUE;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.change_roles(in_user_id TEXT, in_roles TEXT)
  RETURNS BOOLEAN AS $BODY$
DECLARE
  _id TEXT;
  _roles TEXT[];
  _r TEXT;
  _rid BIGINT;
  _rids BIGINT[];
  _b BOOLEAN;
BEGIN
  --check user
  SELECT user_id INTO _id FROM users WHERE user_id = in_user_id;
  IF NOT FOUND THEN
    RETURN FALSE;
  END IF;
  --get roles
  _roles := regexp_split_to_array(in_roles, ',');
  FOREACH _r IN ARRAY _roles LOOP
    SELECT role_id INTO _rid FROM public.roles WHERE name = _r;
    IF _rid IS NOT NULL THEN
      _rids := array_append(_rids, _rid);
    END IF;
  END LOOP;
  --delete roles
  DELETE FROM public.user_role WHERE user_id = in_user_id;
  --add roles
  INSERT INTO public.user_log (user_id, log_event) VALUES (in_user_id, 'clear roles');
  FOREACH _rid IN ARRAY _rids LOOP
    _b := add_role(in_user_id, _rid);
  END LOOP;
  RETURN TRUE;
END;
$BODY$ LANGUAGE plpgsql;

-------
--app--
-------

CREATE OR REPLACE FUNCTION public.create_app(in_app_name TEXT, in_descr TEXT, in_url TEXT)
  RETURNS TEXT AS $BODY$
DECLARE
  _aid TEXT;
  _secret TEXT;
BEGIN
  _aid := md5(in_app_name || in_descr || in_url);
  _secret := crypt(in_app_name || ':' || in_url || ':' || current_timestamp::TEXT, gen_salt('bf'));
  INSERT INTO public.apps (app_id, app_name, descr, app_secret, redirect_uri)
  VALUES (_aid, in_app_name, in_descr, _secret, in_url);
  RETURN _aid;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.delete_app(in_app_id TEXT)
  RETURNS BOOLEAN AS $BODY$
DECLARE
  _aid TEXT;
BEGIN
  SELECT app_id INTO _aid FROM public.apps WHERE app_id = in_app_id;
  IF FOUND THEN
    UPDATE public.apps SET deleted_at = now() WHERE app_id = in_app_id;
    RETURN TRUE;
  END IF;
  RETURN FALSE;
END;
$BODY$ LANGUAGE plpgsql;

---------
--token--
---------

CREATE OR REPLACE FUNCTION public.create_token(in_user_id TEXT, in_app_id TEXT, in_expires INT, in_scope TEXT)
  RETURNS TEXT AS $BODY$
DECLARE
  _s TEXT;
  _token TEXT;
  _exp TIMESTAMP WITH TIME ZONE;
BEGIN
  --check
  SELECT user_id INTO _s FROM public.users WHERE user_id = in_user_id AND deleted_at IS NULL;
  IF NOT FOUND THEN
    RETURN '';
  END IF;
  IF in_app_id IS NOT NULL THEN
    SELECT app_id INTO _s FROM public.apps WHERE app_id = in_app_id AND deleted_at IS NULL;
    IF NOT FOUND THEN
      RETURN '';
    END IF;
  END IF;
  --check exist token
  SELECT expires_in INTO _exp FROM public.access_tokens
  WHERE user_id = in_user_id AND app_id = in_app_id AND expires_in < current_timestamp;
  IF FOUND THEN
    DELETE FROM public.access_tokens
    WHERE user_id = in_user_id AND app_id = in_app_id AND expires_in < current_timestamp;
  END IF;
  IF in_app_id IS NULL THEN
    _token := crypt(in_user_id || ':' || current_timestamp::TEXT, gen_salt('bf'));
  ELSE
    _token := crypt(in_user_id || ':' || in_app_id || ':' || current_timestamp::TEXT, gen_salt('bf'));
  END IF;
  INSERT INTO public.access_tokens (user_id, app_id, access_token, expires_in, scope)
  VALUES (in_user_id, in_app_id, _token, current_timestamp + (in_expires || ' seconds')::INTERVAL, in_scope);
  RETURN _token;
END;
$BODY$ LANGUAGE plpgsql;

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
