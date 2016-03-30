INSERT INTO departments (department_id, name) VALUES ('TS', 'Test');
SELECT create_role('admin', 'Administrator');
SELECT create_role('common_user', 'Common User');
SELECT create_user('admin', 'GW', 'admin');
UPDATE users SET superuser = TRUE WHERE user_id = get_user_id('admin');
SELECT add_role(get_user_id('admin'), get_role_id('admin'));
SELECT create_app('test_app', 'test', '192.168.0.21');
