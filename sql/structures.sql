--Common user database
--version 0.1.0

----------
--tables--
----------

CREATE TABLE public.departments (
  department_id TEXT PRIMARY KEY,
  name TEXT NOT NULL UNIQUE,
  descr TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  deleted_at TIMESTAMP WITH TIME ZONE
);

CREATE TABLE public.roles (
  role_id SERIAL PRIMARY KEY,
  name TEXT NOT NULL UNIQUE,
  descr TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  deleted_at TIMESTAMP WITH TIME ZONE
);

CREATE TABLE public.users (
  user_id TEXT PRIMARY KEY,
  name TEXT NOT NULL UNIQUE,
  password TEXT,
  label TEXT,
  email TEXT,
  descr TEXT,
  department_id TEXT REFERENCES public.departments (department_id),
  superuser BOOLEAN NOT NULL DEFAULT FALSE,
  active BOOLEAN NOT NULL DEFAULT TRUE,
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

CREATE TABLE public.apps (
  app_id TEXT PRIMARY KEY,
  app_name TEXT NOT NULL UNIQUE,
  descr TEXT,
  app_secret TEXT NOT NULL,
  redirect_uri TEXT,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
  deleted_at TIMESTAMP WITH TIME ZONE
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

---------
--views--
---------

CREATE OR REPLACE VIEW public.view_user_role AS
  SELECT user_role.user_id, string_agg(roles.name, ',') AS roles
  FROM user_role JOIN roles ON user_role.role_id = roles.role_id
  GROUP BY user_id;

CREATE OR REPLACE VIEW view_users AS
  SELECT users.user_id, users.name, users.label, password, email,
    users.descr, departments.department_id, departments.name AS department,
    departments.descr AS department_descr, superuser, active, roles,
    users.created_at, users.updated_at
  FROM public.users JOIN public.departments
      ON users.department_id = departments.department_id
    LEFT JOIN public.view_user_role ON users.user_id = view_user_role.user_id
  WHERE users.deleted_at IS NULL;

CREATE OR REPLACE VIEW public.view_user_log AS
  SELECT user_log.user_id, users.name, users.label, email,
    departments.department_id, departments.name AS department,
    from_ip, log_level, log_event, user_log.descr, user_log.created_at
  FROM public.user_log JOIN public.users ON user_log.user_id = users.user_id
    JOIN public.departments ON departments.department_id = users.department_id
  ORDER BY created_at DESC;

CREATE OR REPLACE VIEW public.view_app_tokens AS
  SELECT apps.app_name, apps.descr, access_tokens.user_id, access_tokens.expires_in, access_tokens.scope, access_tokens.created_at
  FROM public.access_tokens LEFT JOIN public.apps ON access_tokens.app_id = apps.app_id;
