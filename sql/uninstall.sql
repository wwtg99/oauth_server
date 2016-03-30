DROP FUNCTION IF EXISTS public.get_user_id(TEXT) CASCADE;
DROP FUNCTION IF EXISTS public.create_user(TEXT, TEXT, TEXT, TEXT, TEXT, BOOLEAN) CASCADE;
DROP FUNCTION IF EXISTS public.delete_user(TEXT) CASCADE;
DROP FUNCTION IF EXISTS public.update_user(TEXT, JSON) CASCADE;
DROP FUNCTION IF EXISTS public.get_role_id(TEXT) CASCADE;
DROP FUNCTION IF EXISTS public.create_role(TEXT, TEXT) CASCADE;
DROP FUNCTION IF EXISTS public.delete_role(BIGINT) CASCADE;
DROP FUNCTION IF EXISTS public.update_role(BIGINT, JSON) CASCADE;
DROP FUNCTION IF EXISTS public.add_role(TEXT, BIGINT) CASCADE;
DROP FUNCTION IF EXISTS public.remove_role(TEXT, BIGINT) CASCADE;
DROP FUNCTION IF EXISTS public.change_roles(TEXT, JSON) CASCADE;
DROP FUNCTION IF EXISTS public.change_roles(TEXT, TEXT) CASCADE;
DROP FUNCTION IF EXISTS public.create_app(TEXT, TEXT, TEXT) CASCADE;
DROP FUNCTION IF EXISTS public.delete_app(TEXT) CASCADE;
DROP FUNCTION IF EXISTS public.create_token(TEXT, TEXT, INT, TEXT) CASCADE;
DROP FUNCTION IF EXISTS public.verify_token(TEXT, TEXT) CASCADE;
DROP SEQUENCE IF EXISTS public.user_id_seq CASCADE;
DROP VIEW IF EXISTS public.view_user_role, view_users, public.view_user_log, public.view_app_tokens CASCADE;
DROP TABLE IF EXISTS public.departments, public.roles, public.users, public.user_role,
public.user_log, public.apps, public.access_tokens CASCADE;