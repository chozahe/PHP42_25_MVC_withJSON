-- Table: public.user

-- DROP TABLE IF EXISTS public."user";


CREATE TABLE IF NOT EXISTS public.users
(
    id SERIAL PRIMARY KEY,
    first_name character varying(100) COLLATE pg_catalog."default" NOT NULL,
    second_name character varying(100) COLLATE pg_catalog."default" NOT NULL,
    age integer,
    job text COLLATE pg_catalog."default",
    email text COLLATE pg_catalog."default"
)
TABLESPACE pg_default;
