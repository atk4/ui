--
-- PostgreSQL database dump
--

-- Dumped from database version 10.4
-- Dumped by pg_dump version 10.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: allocation; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.allocation (
    id integer NOT NULL,
    payment_id integer,
    invoice_id integer,
    allocated numeric(8,2)
);


ALTER TABLE public.allocation OWNER TO root;

--
-- Name: allocation_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.allocation_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.allocation_id_seq OWNER TO root;

--
-- Name: allocation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.allocation_id_seq OWNED BY public.allocation.id;


--
-- Name: client; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.client (
    id integer NOT NULL,
    name text,
    address text
);


ALTER TABLE public.client OWNER TO root;

--
-- Name: client_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.client_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.client_id_seq OWNER TO root;

--
-- Name: client_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.client_id_seq OWNED BY public.client.id;


--
-- Name: country; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.country (
    id integer NOT NULL,
    iso text,
    name text,
    nicename text,
    iso3 text,
    numcode integer,
    phonecode integer
);


ALTER TABLE public.country OWNER TO root;

--
-- Name: country_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.country_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.country_id_seq OWNER TO root;

--
-- Name: country_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.country_id_seq OWNED BY public.country.id;


--
-- Name: invoice; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.invoice (
    id integer NOT NULL,
    ref_no text,
    status text,
    client_id integer
);


ALTER TABLE public.invoice OWNER TO root;

--
-- Name: invoice_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.invoice_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.invoice_id_seq OWNER TO root;

--
-- Name: invoice_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.invoice_id_seq OWNED BY public.invoice.id;


--
-- Name: line; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.line (
    id integer NOT NULL,
    invoice_id integer,
    item text,
    qty integer,
    price numeric(8,2)
);


ALTER TABLE public.line OWNER TO root;

--
-- Name: line_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.line_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.line_id_seq OWNER TO root;

--
-- Name: line_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.line_id_seq OWNED BY public.line.id;


--
-- Name: payment; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.payment (
    id integer NOT NULL,
    ref_no text,
    status text,
    amount numeric(8,2),
    client_id integer
);


ALTER TABLE public.payment OWNER TO root;

--
-- Name: payment_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.payment_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.payment_id_seq OWNER TO root;

--
-- Name: payment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.payment_id_seq OWNED BY public.payment.id;


--
-- Name: test; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.test (
    id integer NOT NULL,
    name text,
    email text
);


ALTER TABLE public.test OWNER TO root;

--
-- Name: test_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.test_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.test_id_seq OWNER TO root;

--
-- Name: test_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.test_id_seq OWNED BY public.test.id;


--
-- Name: allocation id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.allocation ALTER COLUMN id SET DEFAULT nextval('public.allocation_id_seq'::regclass);


--
-- Name: client id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.client ALTER COLUMN id SET DEFAULT nextval('public.client_id_seq'::regclass);


--
-- Name: country id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.country ALTER COLUMN id SET DEFAULT nextval('public.country_id_seq'::regclass);


--
-- Name: invoice id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.invoice ALTER COLUMN id SET DEFAULT nextval('public.invoice_id_seq'::regclass);


--
-- Name: line id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.line ALTER COLUMN id SET DEFAULT nextval('public.line_id_seq'::regclass);


--
-- Name: payment id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.payment ALTER COLUMN id SET DEFAULT nextval('public.payment_id_seq'::regclass);


--
-- Name: test id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.test ALTER COLUMN id SET DEFAULT nextval('public.test_id_seq'::regclass);


--
-- Data for Name: allocation; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.allocation (id, payment_id, invoice_id, allocated) FROM stdin;
1	1	4	20.00
\.


--
-- Data for Name: client; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.client (id, name, address) FROM stdin;
2	Client 2	another big\nmultiline\naddress
1	John Smith	Hello world address here blah blaht
4	three	\N
5	four	\N
6	five	\N
7	six	\N
8	seven	\N
9	eight	\N
10	nine	\N
11	ten	\N
12	eleven	\N
13	clie	\N
3	aaa	aoeu
\.


--
-- Data for Name: country; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.country (id, iso, name, nicename, iso3, numcode, phonecode) FROM stdin;
1	TH	HELLO	hello	THT	234	\N
2	TH	HELLOU	helloU	THT	234	\N
\.


--
-- Data for Name: invoice; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.invoice (id, ref_no, status, client_id) FROM stdin;
3	rxx	partial	3
2	refx xxxxxx	paid	3
4	Inv 1	paid	1
5	inv39	draft	2
\.


--
-- Data for Name: line; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.line (id, invoice_id, item, qty, price) FROM stdin;
1	5	aoeuaeo	4	20.00
\.


--
-- Data for Name: payment; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.payment (id, ref_no, status, amount, client_id) FROM stdin;
1	XX	draft	200.00	2
\.


--
-- Data for Name: test; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.test (id, name, email) FROM stdin;
1	roman	tenst
\.


--
-- Name: allocation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.allocation_id_seq', 1, true);


--
-- Name: client_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.client_id_seq', 13, true);


--
-- Name: country_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.country_id_seq', 2, true);


--
-- Name: invoice_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.invoice_id_seq', 5, true);


--
-- Name: line_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.line_id_seq', 1, true);


--
-- Name: payment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.payment_id_seq', 1, true);


--
-- Name: test_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.test_id_seq', 1, true);


--
-- Name: allocation allocation_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.allocation
    ADD CONSTRAINT allocation_pkey PRIMARY KEY (id);


--
-- Name: client client_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.client
    ADD CONSTRAINT client_pkey PRIMARY KEY (id);


--
-- Name: country country_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.country
    ADD CONSTRAINT country_pkey PRIMARY KEY (id);


--
-- Name: invoice invoice_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.invoice
    ADD CONSTRAINT invoice_pkey PRIMARY KEY (id);


--
-- Name: line line_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.line
    ADD CONSTRAINT line_pkey PRIMARY KEY (id);


--
-- Name: payment payment_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.payment
    ADD CONSTRAINT payment_pkey PRIMARY KEY (id);


--
-- Name: test test_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.test
    ADD CONSTRAINT test_pkey PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--

