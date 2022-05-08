--
-- PostgreSQL database cluster dump
--

SET default_transaction_read_only = off;

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;

--
-- Drop databases (except postgres and template1)
--

DROP DATABASE chat;




--
-- Drop roles
--

DROP ROLE admin;


--
-- Roles
--

CREATE ROLE admin;
ALTER ROLE admin WITH SUPERUSER INHERIT CREATEROLE CREATEDB LOGIN REPLICATION BYPASSRLS PASSWORD 'SCRAM-SHA-256$4096:hyVpMvYyJyFnII6t/ekEDw==$N2zs0OxihfXhxYP5ubH4gSP18Uqs7oef46iriRILU8Y=:V253DRFeLzhxIMRBgEbps/17v1upQpNQ9DKuxGkGnHE=';






--
-- Databases
--

--
-- Database "template1" dump
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 14.2 (Debian 14.2-1.pgdg110+1)
-- Dumped by pg_dump version 14.2 (Debian 14.2-1.pgdg110+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

UPDATE pg_catalog.pg_database SET datistemplate = false WHERE datname = 'template1';
DROP DATABASE template1;
--
-- Name: template1; Type: DATABASE; Schema: -; Owner: admin
--

CREATE DATABASE template1 WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'en_US.utf8';


ALTER DATABASE template1 OWNER TO admin;

\connect template1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: DATABASE template1; Type: COMMENT; Schema: -; Owner: admin
--

COMMENT ON DATABASE template1 IS 'default template for new databases';


--
-- Name: template1; Type: DATABASE PROPERTIES; Schema: -; Owner: admin
--

ALTER DATABASE template1 IS_TEMPLATE = true;


\connect template1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: DATABASE template1; Type: ACL; Schema: -; Owner: admin
--

REVOKE CONNECT,TEMPORARY ON DATABASE template1 FROM PUBLIC;
GRANT CONNECT ON DATABASE template1 TO PUBLIC;


--
-- PostgreSQL database dump complete
--

--
-- Database "chat" dump
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 14.2 (Debian 14.2-1.pgdg110+1)
-- Dumped by pg_dump version 14.2 (Debian 14.2-1.pgdg110+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: chat; Type: DATABASE; Schema: -; Owner: admin
--

CREATE DATABASE chat WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'en_US.utf8';


ALTER DATABASE chat OWNER TO admin;

\connect chat

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: chat_roles; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.chat_roles (
    id integer NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.chat_roles OWNER TO admin;

--
-- Name: chats; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.chats (
    id integer NOT NULL,
    is_secret boolean DEFAULT false NOT NULL
);


ALTER TABLE public.chats OWNER TO admin;

--
-- Name: chats_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.chats_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.chats_id_seq OWNER TO admin;

--
-- Name: chats_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.chats_id_seq OWNED BY public.chats.id;


--
-- Name: chats_users; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.chats_users (
    chat_id integer NOT NULL,
    user_id integer NOT NULL,
    unread_count integer DEFAULT 0,
    role smallint DEFAULT 2 NOT NULL,
    is_blocked boolean DEFAULT false
);


ALTER TABLE public.chats_users OWNER TO admin;

--
-- Name: messages; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.messages (
    id integer NOT NULL,
    content character varying(512) NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    chat_id integer NOT NULL,
    sender_user_id integer NOT NULL,
    is_edited boolean DEFAULT false NOT NULL,
    is_deleted boolean DEFAULT false NOT NULL,
    type smallint DEFAULT 1 NOT NULL
);


ALTER TABLE public.messages OWNER TO admin;

--
-- Name: messages_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.messages_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.messages_id_seq OWNER TO admin;

--
-- Name: messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.messages_id_seq OWNED BY public.messages.id;


--
-- Name: messages_stats; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.messages_stats (
    message_id integer NOT NULL,
    user_id integer NOT NULL,
    is_read boolean DEFAULT false NOT NULL,
    is_deleted boolean DEFAULT false NOT NULL
);


ALTER TABLE public.messages_stats OWNER TO admin;

--
-- Name: messages_types; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.messages_types (
    id integer NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.messages_types OWNER TO admin;

--
-- Name: chats id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.chats ALTER COLUMN id SET DEFAULT nextval('public.chats_id_seq'::regclass);


--
-- Name: messages id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.messages ALTER COLUMN id SET DEFAULT nextval('public.messages_id_seq'::regclass);


--
-- Data for Name: chat_roles; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.chat_roles (id, name) FROM stdin;
1	owner
2	user
3	admin
\.


--
-- Data for Name: chats; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.chats (id, is_secret) FROM stdin;
\.


--
-- Data for Name: chats_users; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.chats_users (chat_id, user_id, unread_count, role, is_blocked) FROM stdin;
\.


--
-- Data for Name: messages; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.messages (id, content, created_at, chat_id, sender_user_id, is_edited, is_deleted, type) FROM stdin;
\.


--
-- Data for Name: messages_stats; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.messages_stats (message_id, user_id, is_read, is_deleted) FROM stdin;
\.


--
-- Data for Name: messages_types; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.messages_types (id, name) FROM stdin;
1	text
2	file
3	img
\.


--
-- Name: chats_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.chats_id_seq', 77, true);


--
-- Name: messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.messages_id_seq', 1193, true);


--
-- Name: chats chats_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.chats
    ADD CONSTRAINT chats_pkey PRIMARY KEY (id);


--
-- Name: messages_stats local_deleted_messages; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.messages_stats
    ADD CONSTRAINT local_deleted_messages UNIQUE (message_id, user_id);


--
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id);


--
-- Name: chats_users unique_users_in_chats; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.chats_users
    ADD CONSTRAINT unique_users_in_chats UNIQUE (chat_id, user_id);


--
-- Name: chats_users chats_users_chat_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.chats_users
    ADD CONSTRAINT chats_users_chat_id_fkey FOREIGN KEY (chat_id) REFERENCES public.chats(id) ON DELETE CASCADE;


--
-- Name: messages messages_chat_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_chat_id_fkey FOREIGN KEY (chat_id) REFERENCES public.chats(id) ON DELETE CASCADE;


--
-- Name: messages_stats messages_stats_message_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.messages_stats
    ADD CONSTRAINT messages_stats_message_id_fkey FOREIGN KEY (message_id) REFERENCES public.messages(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

--
-- Database "postgres" dump
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 14.2 (Debian 14.2-1.pgdg110+1)
-- Dumped by pg_dump version 14.2 (Debian 14.2-1.pgdg110+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

DROP DATABASE postgres;
--
-- Name: postgres; Type: DATABASE; Schema: -; Owner: admin
--

CREATE DATABASE postgres WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'en_US.utf8';


ALTER DATABASE postgres OWNER TO admin;

\connect postgres

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: DATABASE postgres; Type: COMMENT; Schema: -; Owner: admin
--

COMMENT ON DATABASE postgres IS 'default administrative connection database';


--
-- PostgreSQL database dump complete
--

--
-- PostgreSQL database cluster dump complete
--

