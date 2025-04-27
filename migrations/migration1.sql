-- Table: public.user

-- DROP TABLE IF EXISTS public."user";

CREATE TABLE IF NOT EXISTS public."users"
(
    id integer NOT NULL DEFAULT nextval('user_id_seq'::regclass),
    first_name character varying(100) COLLATE pg_catalog."default" NOT NULL,
    second_name character varying(100) COLLATE pg_catalog."default" NOT NULL,
    age integer,
    job text COLLATE pg_catalog."default",
    email text COLLATE pg_catalog."default",
    CONSTRAINT user_pkey PRIMARY KEY (id)
)

    TABLESPACE pg_default;

ALTER TABLE IF EXISTS public."users"
    OWNER to postgres;