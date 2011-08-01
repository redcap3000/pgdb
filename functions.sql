DROP FUNCTION IF EXISTS show_columns(varchar(255));

CREATE FUNCTION show_columns(varchar(255)) returns table(table_catalog text,table_schema text,table_name text,column_name text,ordinal_position int,
column_default text,is_nullable text,data_type text,character_maximum_length int,character_octet_length int,numeric_precision int,numeric_precision_radix int,
numeric_scale int,datetime_precision int,interval_type text,interval_precision text,character_set_catalog text,character_set_schema text,character_set_name text,
collation_catalog text,collation_schema text,collation_name text,domain_catalog text,domain_schema text,domain_name text,udt_catalog text,udt_schema text,
udt_name text,scope_catalog text,scope_schema text,scope_name text,maximum_cardinality int,
dtd_identifier text,is_self_referencing text,is_identity text,identity_generation text,identity_start text,
identity_increment text,identity_maximum text,identity_minimum text,identity_cycle text,is_generated text,generation_expression text,is_updatable text)
	AS $$
	SELECT * FROM information_schema.columns WHERE table_name = $1
	$$
	LANGUAGE SQL;


DROP FUNCTION IF EXISTS  show_tables();

CREATE FUNCTION show_tables() RETURNS
table(table_catalog varchar(512),table_schema varchar(512),table_name varchar(512),table_type varchar(512), self_referencing_column_name varchar(512),
 reference_generation varchar(512), user_defined_type_catalog varchar(512),user_defined_type_schema varchar(512),
 user_defined_type_name varchar(512), is_insertable_into varchar(3), is_typed varchar(3), commit_action varchar(512))
	AS $$
	SELECT * FROM information_schema.tables WHERE table_schema = 'public'
	$$
	LANGUAGE SQL;

DROP FUNCTION IF EXISTS show_table(varchar(255));

CREATE FUNCTION show_table(varchar(255)) RETURNS 
table(table_catalog varchar(512),table_schema varchar(512),table_name varchar(512),table_type varchar(512), self_referencing_column_name varchar(512),
 reference_generation varchar(512), user_defined_type_catalog varchar(512),user_defined_type_schema varchar(512),
 user_defined_type_name varchar(512), is_insertable_into varchar(3), is_typed varchar(3), commit_action varchar(512))
	AS $$
	SELECT * FROM information_schema.tables WHERE table_schema = 'public' and table_name = $1
	$$
	LANGUAGE SQL;



    
    
