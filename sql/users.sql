--users and roles for Postgresql


----------
--tables--
----------

CREATE TABLE public.departments (
  department_id TEXT PRIMARY KEY,
  name TEXT NOT NULL UNIQUE,
  descr TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
);

CREATE TABLE public.roles (
  role_id SERIAL PRIMARY KEY,
  name TEXT NOT NULL UNIQUE,
  descr TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
);

CREATE TABLE public.users (
  user_id TEXT PRIMARY KEY,
  name TEXT NOT NULL UNIQUE,
  password TEXT,
  label TEXT,
  email TEXT,
  descr TEXT,
  department_id TEXT REFERENCES public.departments (department_id) ON UPDATE CASCADE,
  superuser BOOLEAN NOT NULL DEFAULT FALSE,
  remember_token TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  deleted_at TIMESTAMP WITH TIME ZONE
);

CREATE SEQUENCE public.user_id_seq;

CREATE TABLE public.user_role (
  user_id TEXT NOT NULL REFERENCES public.users (user_id),
  role_id BIGINT NOT NULL REFERENCES public.roles (role_id),
  PRIMARY KEY (user_id, role_id)
);

CREATE TABLE public.user_log (
  id SERIAL PRIMARY KEY,
  user_id TEXT NOT NULL REFERENCES public.users (user_id),
  from_ip CIDR,
  log_level TEXT NOT NULL DEFAULT 'INFO',
  log_event TEXT,
  descr TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
);

CREATE INDEX ON public.user_log (user_id);

-------------
--functions--
-------------

CREATE OR REPLACE FUNCTION public.get_department_id(in_department TEXT)
  RETURNS TEXT AS $BODY$
DECLARE
  _id TEXT;
BEGIN
  SELECT department_id INTO _id FROM public.departments WHERE name = in_department;
  IF NOT FOUND THEN
    RAISE EXCEPTION 'There is no department % found!', in_department;
  ELSE
    RETURN _id;
  END IF;
END;
$BODY$ LANGUAGE plpgsql
SECURITY DEFINER;

CREATE OR REPLACE FUNCTION public.get_role_id(in_role TEXT)
  RETURNS BIGINT AS $BODY$
DECLARE
  _id BIGINT;
BEGIN
  SELECT role_id INTO _id FROM public.roles WHERE name = in_role;
  IF NOT FOUND THEN
    RAISE EXCEPTION 'There is no role % found!', in_role;
  ELSE
    RETURN _id;
  END IF;
END;
$BODY$ LANGUAGE plpgsql
SECURITY DEFINER;

CREATE OR REPLACE FUNCTION public.get_user_id(in_user TEXT)
  RETURNS TEXT AS $BODY$
DECLARE
  _id TEXT;
BEGIN
  SELECT user_id INTO _id FROM public.users WHERE name = in_user AND deleted_at IS NULL;
  IF NOT FOUND THEN
    RAISE EXCEPTION 'There is no user % found!', in_user;
  ELSE
    RETURN _id;
  END IF;
END;
$BODY$ LANGUAGE plpgsql
SECURITY DEFINER;

CREATE OR REPLACE FUNCTION public.active_user(in_user_id TEXT)
  RETURNS BOOLEAN AS $BODY$
DECLARE
  _id TEXT;
BEGIN
  SELECT user_id INTO _id FROM public.users WHERE user_id = in_user_id AND deleted_at IS NOT NULL;
  IF FOUND THEN
    UPDATE public.users SET deleted_at = NULL WHERE user_id = _id;
    RETURN TRUE;
  END IF;
  RETURN FALSE;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.add_user_role(in_user_id TEXT, in_role_id BIGINT)
  RETURNS BOOLEAN AS $BODY$
DECLARE
  _id TEXT;
BEGIN
  SELECT user_id INTO _id FROM public.user_role WHERE user_id = in_user_id AND role_id = in_role_id;
  IF NOT FOUND THEN
    INSERT INTO public.user_role (user_id, role_id) VALUES (in_user_id, in_role_id);
  END IF;
  RETURN TRUE;
END;
$BODY$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.delete_user_role(in_user_id TEXT, in_role_id BIGINT)
  RETURNS BOOLEAN AS $BODY$
DECLARE
  _id TEXT;
BEGIN
  SELECT user_id INTO _id FROM public.user_role WHERE user_id = in_user_id AND role_id = in_role_id;
  IF FOUND THEN
    DELETE FROM public.user_role WHERE user_id = in_user_id AND role_id = in_role_id;
  END IF;
  RETURN TRUE;
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
BEGIN
  --check user
  SELECT user_id INTO _id FROM public.users WHERE user_id = in_user_id;
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
  FOREACH _rid IN ARRAY _rids LOOP
    INSERT INTO public.user_role (user_id, role_id) VALUES (_id, _rid);
  END LOOP;
  RETURN TRUE;
END;
$BODY$ LANGUAGE plpgsql;

------------
--triggers--
------------

CREATE OR REPLACE FUNCTION public.tp_change_department() RETURNS TRIGGER AS $BODY$
DECLARE
BEGIN
  CASE TG_OP
    WHEN 'INSERT' THEN
      NEW.created_at = now();
      NEW.updated_at = now();
      RETURN NEW;
    WHEN 'UPDATE' THEN
      NEW.updated_at = now();
      RETURN NEW;
    ELSE
      RETURN NULL;
  END CASE;
END;
$BODY$ LANGUAGE plpgsql
SECURITY DEFINER;

CREATE TRIGGER tg_department BEFORE INSERT OR UPDATE ON public.departments
FOR EACH ROW EXECUTE PROCEDURE public.tp_change_department();

CREATE OR REPLACE FUNCTION public.tp_change_role() RETURNS TRIGGER AS $BODY$
DECLARE
BEGIN
  CASE TG_OP
    WHEN 'INSERT' THEN
      NEW.created_at = now();
      NEW.updated_at = now();
      RETURN NEW;
    WHEN 'UPDATE' THEN
      NEW.updated_at = now();
      RETURN NEW;
    ELSE
      RETURN NULL;
  END CASE;
END;
$BODY$ LANGUAGE plpgsql
SECURITY DEFINER;

CREATE TRIGGER tg_role BEFORE INSERT OR UPDATE ON public.roles
FOR EACH ROW EXECUTE PROCEDURE public.tp_change_role();

CREATE OR REPLACE FUNCTION public.tp_change_user() RETURNS TRIGGER AS $BODY$
DECLARE
  _id TEXT;
  _s BIGINT;
BEGIN
  CASE TG_OP
    WHEN 'INSERT' THEN
      _s := nextval('user_id_seq');
      _id := 'U' || lpad(_s::TEXT, 6, '0');
      NEW.user_id = _id;
      NEW.created_at = now();
      NEW.updated_at = now();
      RETURN NEW;
    WHEN 'UPDATE' THEN
      NEW.user_id = OLD.user_id;
      NEW.updated_at = now();
      INSERT INTO public.user_log (user_id, log_event, descr) VALUES (NEW.user_id, 'update', row_to_json(NEW)::TEXT);
      RETURN NEW;
    WHEN 'DELETE' THEN
      UPDATE public.users SET deleted_at = now() WHERE user_id = OLD.user_id;
      INSERT INTO public.user_log (user_id, log_event) VALUES (OLD.user_id, 'delete');
      RETURN NULL;
  END CASE;
END;
$BODY$ LANGUAGE plpgsql
SECURITY DEFINER;

CREATE TRIGGER tg_user BEFORE INSERT OR UPDATE OR DELETE ON public.users
FOR EACH ROW EXECUTE PROCEDURE public.tp_change_user();

---------
--views--
---------

CREATE OR REPLACE VIEW public.view_user_role AS
  SELECT user_role.user_id, string_agg(roles.name, ',') AS roles
  FROM user_role JOIN roles ON user_role.role_id = roles.role_id
  GROUP BY user_id;

CREATE OR REPLACE VIEW public.view_users AS
  SELECT users.user_id, users.name, users.label, password, email,
    users.descr, departments.department_id, departments.name AS department,
    departments.descr AS department_descr, superuser, roles, remember_token,
    users.created_at, users.updated_at, users.deleted_at
  FROM public.users LEFT JOIN public.departments ON users.department_id = departments.department_id
    LEFT JOIN public.view_user_role ON users.user_id = view_user_role.user_id;

CREATE OR REPLACE VIEW public.view_user_log AS
  SELECT user_log.user_id, users.name, users.label, email,
    departments.department_id, departments.name AS department,
    from_ip, log_level, log_event, user_log.descr, user_log.created_at
  FROM public.user_log JOIN public.users ON user_log.user_id = users.user_id
    JOIN public.departments ON departments.department_id = users.department_id
  ORDER BY created_at DESC;
