--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: users; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA users;


ALTER SCHEMA users OWNER TO postgres;

--
-- Name: utils; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA utils;


ALTER SCHEMA utils OWNER TO postgres;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: ltree; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS ltree WITH SCHEMA public;


--
-- Name: EXTENSION ltree; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION ltree IS 'data type for hierarchical tree-like structures';


SET search_path = utils, pg_catalog;

--
-- Name: random_string(integer); Type: FUNCTION; Schema: utils; Owner: postgres
--

CREATE FUNCTION random_string(length integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
declare
  chars text[] := '{0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z}';
  result text := '';
  i integer := 0;
begin
  if length < 0 then
    raise exception 'Given length cannot be less than 0';
  end if;
  for i in 1..length loop
    result := result || chars[1+random()*(array_length(chars, 1)-1)];
  end loop;
  return result;
end;
$$;


ALTER FUNCTION utils.random_string(length integer) OWNER TO postgres;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: adv; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE adv (
    id integer NOT NULL,
    owner integer NOT NULL,
    category character varying(255),
    name character varying(255),
    descr text,
    images character varying(255)[],
    snap timestamp(6) with time zone DEFAULT now() NOT NULL
);


ALTER TABLE adv OWNER TO postgres;

--
-- Name: adv_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE adv_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE adv_id_seq OWNER TO postgres;

--
-- Name: adv_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE adv_id_seq OWNED BY adv.id;


--
-- Name: categories; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE categories (
    id smallint NOT NULL,
    parent smallint NOT NULL,
    title character varying NOT NULL,
    tree ltree
);


ALTER TABLE categories OWNER TO postgres;

SET search_path = users, pg_catalog;

--
-- Name: auth.vk; Type: TABLE; Schema: users; Owner: postgres; Tablespace: 
--

CREATE TABLE "auth.vk" (
    uid integer NOT NULL,
    vkid integer NOT NULL,
    token character varying(255) NOT NULL,
    expires timestamp(6) with time zone
);


ALTER TABLE "auth.vk" OWNER TO postgres;

--
-- Name: info; Type: TABLE; Schema: users; Owner: postgres; Tablespace: 
--

CREATE TABLE info (
    uid integer NOT NULL,
    name character varying(255)[],
    avatar character varying(255)[],
    city character varying(255)
);


ALTER TABLE info OWNER TO postgres;

--
-- Name: temp; Type: TABLE; Schema: users; Owner: postgres; Tablespace: 
--

CREATE TABLE temp (
    tid bigint NOT NULL,
    uid integer
);


ALTER TABLE temp OWNER TO postgres;

--
-- Name: users.temp_tid_seq; Type: SEQUENCE; Schema: users; Owner: postgres
--

CREATE SEQUENCE "users.temp_tid_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "users.temp_tid_seq" OWNER TO postgres;

--
-- Name: users.temp_tid_seq; Type: SEQUENCE OWNED BY; Schema: users; Owner: postgres
--

ALTER SEQUENCE "users.temp_tid_seq" OWNED BY temp.tid;


--
-- Name: users_uid_seq; Type: SEQUENCE; Schema: users; Owner: postgres
--

CREATE SEQUENCE users_uid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE users_uid_seq OWNER TO postgres;

--
-- Name: users_uid_seq; Type: SEQUENCE OWNED BY; Schema: users; Owner: postgres
--

ALTER SEQUENCE users_uid_seq OWNED BY info.uid;


SET search_path = utils, pg_catalog;

--
-- Name: images; Type: TABLE; Schema: utils; Owner: postgres; Tablespace: 
--

CREATE TABLE images (
    iid integer NOT NULL,
    author integer,
    name character varying(255) DEFAULT random_string(6) NOT NULL,
    ext character varying(255) NOT NULL,
    snap timestamp(6) with time zone DEFAULT now() NOT NULL
);


ALTER TABLE images OWNER TO postgres;

--
-- Name: images_id_seq; Type: SEQUENCE; Schema: utils; Owner: postgres
--

CREATE SEQUENCE images_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE images_id_seq OWNER TO postgres;

--
-- Name: images_id_seq; Type: SEQUENCE OWNED BY; Schema: utils; Owner: postgres
--

ALTER SEQUENCE images_id_seq OWNED BY images.iid;


SET search_path = public, pg_catalog;

--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY adv ALTER COLUMN id SET DEFAULT nextval('adv_id_seq'::regclass);


SET search_path = users, pg_catalog;

--
-- Name: uid; Type: DEFAULT; Schema: users; Owner: postgres
--

ALTER TABLE ONLY info ALTER COLUMN uid SET DEFAULT nextval('users_uid_seq'::regclass);


--
-- Name: tid; Type: DEFAULT; Schema: users; Owner: postgres
--

ALTER TABLE ONLY temp ALTER COLUMN tid SET DEFAULT nextval('"users.temp_tid_seq"'::regclass);


SET search_path = utils, pg_catalog;

--
-- Name: iid; Type: DEFAULT; Schema: utils; Owner: postgres
--

ALTER TABLE ONLY images ALTER COLUMN iid SET DEFAULT nextval('images_id_seq'::regclass);


SET search_path = public, pg_catalog;

--
-- Name: adv_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY adv
    ADD CONSTRAINT adv_pkey PRIMARY KEY (id);


--
-- Name: categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


SET search_path = users, pg_catalog;

--
-- Name: users.temp_pkey; Type: CONSTRAINT; Schema: users; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY temp
    ADD CONSTRAINT "users.temp_pkey" PRIMARY KEY (tid);


--
-- Name: users.vk_pkey; Type: CONSTRAINT; Schema: users; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "auth.vk"
    ADD CONSTRAINT "users.vk_pkey" PRIMARY KEY (uid);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: users; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY info
    ADD CONSTRAINT users_pkey PRIMARY KEY (uid);


SET search_path = utils, pg_catalog;

--
-- Name: images_name_idx; Type: INDEX; Schema: utils; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX images_name_idx ON images USING btree (name);


SET search_path = public, pg_catalog;

--
-- Name: adv_owner_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY adv
    ADD CONSTRAINT adv_owner_fkey FOREIGN KEY (owner) REFERENCES users.info(uid) ON UPDATE CASCADE ON DELETE CASCADE;


SET search_path = users, pg_catalog;

--
-- Name: auth.vk_uid_fkey; Type: FK CONSTRAINT; Schema: users; Owner: postgres
--

ALTER TABLE ONLY "auth.vk"
    ADD CONSTRAINT "auth.vk_uid_fkey" FOREIGN KEY (uid) REFERENCES info(uid) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users.temp_uid_fkey; Type: FK CONSTRAINT; Schema: users; Owner: postgres
--

ALTER TABLE ONLY temp
    ADD CONSTRAINT "users.temp_uid_fkey" FOREIGN KEY (uid) REFERENCES info(uid) ON UPDATE CASCADE ON DELETE CASCADE;


SET search_path = utils, pg_catalog;

--
-- Name: images_author_fkey; Type: FK CONSTRAINT; Schema: utils; Owner: postgres
--

ALTER TABLE ONLY images
    ADD CONSTRAINT images_author_fkey FOREIGN KEY (author) REFERENCES users.info(uid) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

