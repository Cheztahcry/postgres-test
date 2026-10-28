--
-- PostgreSQL database dump
--

\restrict RlatAdjEDM7V7vhn1GgPM0jdryhi3lG5nojqRx1dvkMtifOyPZgkuELitNsn5Z8

-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE ONLY public.borrow_transactions DROP CONSTRAINT borrow_transactions_borrower_id_fkey;
ALTER TABLE ONLY public.borrow_items DROP CONSTRAINT borrow_items_equipment_id_fkey;
ALTER TABLE ONLY public.borrow_items DROP CONSTRAINT borrow_items_borrow_id_fkey;
ALTER TABLE ONLY public.equipment DROP CONSTRAINT equipment_property_no_key;
ALTER TABLE ONLY public.equipment DROP CONSTRAINT equipment_pkey;
ALTER TABLE ONLY public.borrowers DROP CONSTRAINT borrowers_pkey;
ALTER TABLE ONLY public.borrowers DROP CONSTRAINT borrowers_borrower_no_key;
ALTER TABLE ONLY public.borrow_transactions DROP CONSTRAINT borrow_transactions_pkey;
ALTER TABLE ONLY public.borrow_items DROP CONSTRAINT borrow_items_pkey;
ALTER TABLE ONLY public.borrow_items DROP CONSTRAINT borrow_items_borrow_id_equipment_id_key;
ALTER TABLE public.equipment ALTER COLUMN equipment_id DROP DEFAULT;
ALTER TABLE public.borrowers ALTER COLUMN borrower_id DROP DEFAULT;
ALTER TABLE public.borrow_transactions ALTER COLUMN borrow_id DROP DEFAULT;
ALTER TABLE public.borrow_items ALTER COLUMN borrow_item_id DROP DEFAULT;
DROP SEQUENCE public.equipment_equipment_id_seq;
DROP TABLE public.equipment;
DROP SEQUENCE public.borrowers_borrower_id_seq;
DROP TABLE public.borrowers;
DROP SEQUENCE public.borrow_transactions_borrow_id_seq;
DROP TABLE public.borrow_transactions;
DROP SEQUENCE public.borrow_items_borrow_item_id_seq;
DROP TABLE public.borrow_items;
SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: borrow_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.borrow_items (
    borrow_item_id integer NOT NULL,
    borrow_id integer NOT NULL,
    equipment_id integer NOT NULL,
    quantity integer NOT NULL,
    CONSTRAINT borrow_items_quantity_check CHECK ((quantity > 0))
);


ALTER TABLE public.borrow_items OWNER TO postgres;

--
-- Name: borrow_items_borrow_item_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.borrow_items_borrow_item_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.borrow_items_borrow_item_id_seq OWNER TO postgres;

--
-- Name: borrow_items_borrow_item_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.borrow_items_borrow_item_id_seq OWNED BY public.borrow_items.borrow_item_id;


--
-- Name: borrow_transactions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.borrow_transactions (
    borrow_id integer NOT NULL,
    borrower_id integer NOT NULL,
    borrow_date date DEFAULT CURRENT_DATE NOT NULL,
    due_date date NOT NULL,
    status character varying(50) DEFAULT 'Borrowed'::character varying NOT NULL
);


ALTER TABLE public.borrow_transactions OWNER TO postgres;

--
-- Name: borrow_transactions_borrow_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.borrow_transactions_borrow_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.borrow_transactions_borrow_id_seq OWNER TO postgres;

--
-- Name: borrow_transactions_borrow_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.borrow_transactions_borrow_id_seq OWNED BY public.borrow_transactions.borrow_id;


--
-- Name: borrowers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.borrowers (
    borrower_id integer NOT NULL,
    borrower_no character varying(50) NOT NULL,
    last_name character varying(150) NOT NULL,
    first_name character varying(150) NOT NULL,
    borrower_type character varying(50) NOT NULL
);


ALTER TABLE public.borrowers OWNER TO postgres;

--
-- Name: borrowers_borrower_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.borrowers_borrower_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.borrowers_borrower_id_seq OWNER TO postgres;

--
-- Name: borrowers_borrower_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.borrowers_borrower_id_seq OWNED BY public.borrowers.borrower_id;


--
-- Name: equipment; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.equipment (
    equipment_id integer NOT NULL,
    property_no character varying(50) NOT NULL,
    equipment_name character varying(150) NOT NULL,
    quantity_available integer DEFAULT 0 NOT NULL,
    CONSTRAINT equipment_quantity_available_check CHECK ((quantity_available >= 0))
);


ALTER TABLE public.equipment OWNER TO postgres;

--
-- Name: equipment_equipment_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.equipment_equipment_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.equipment_equipment_id_seq OWNER TO postgres;

--
-- Name: equipment_equipment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.equipment_equipment_id_seq OWNED BY public.equipment.equipment_id;


--
-- Name: borrow_items borrow_item_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.borrow_items ALTER COLUMN borrow_item_id SET DEFAULT nextval('public.borrow_items_borrow_item_id_seq'::regclass);


--
-- Name: borrow_transactions borrow_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.borrow_transactions ALTER COLUMN borrow_id SET DEFAULT nextval('public.borrow_transactions_borrow_id_seq'::regclass);


--
-- Name: borrowers borrower_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.borrowers ALTER COLUMN borrower_id SET DEFAULT nextval('public.borrowers_borrower_id_seq'::regclass);


--
-- Name: equipment equipment_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipment ALTER COLUMN equipment_id SET DEFAULT nextval('public.equipment_equipment_id_seq'::regclass);


--
-- Data for Name: borrow_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.borrow_items (borrow_item_id, borrow_id, equipment_id, quantity) FROM stdin;
\.


--
-- Data for Name: borrow_transactions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.borrow_transactions (borrow_id, borrower_id, borrow_date, due_date, status) FROM stdin;
1	11	2026-09-29	2027-09-07	Borrowed
2	12	2026-09-30	2026-10-01	Borrowed
3	13	2026-09-30	2026-10-01	Borrowed
4	14	2026-09-30	2026-10-02	Borrowed
5	15	2026-09-30	2026-10-09	Borrowed
\.


--
-- Data for Name: borrowers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.borrowers (borrower_id, borrower_no, last_name, first_name, borrower_type) FROM stdin;
1	a8481edb-33a2-49d9-ab1c-fa3c80b91cf5	Rojo	Chester	Student
2	BR-922581	Rojo	Chester	Student
8	BR-033182	Rojo	Arthur	Student
11	BR-596682	Rojo	Chester	Faculty
12	BR-062029	Brad	Brad	Student
13	BR-418462	Bambirden	Lenny	Guest
14	BR-419172	Morgan	Arthur	Staff
15	BR-551183	Bambirden	Arthur	Faculty
\.


--
-- Data for Name: equipment; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.equipment (equipment_id, property_no, equipment_name, quantity_available) FROM stdin;
1	PRJ-01	Standard Room Projector	1
3	CAB-01	HDMI Cable (2 meters)	1
2	PRJ-02	Mini Portable Projector	4
5	MIC-01	Wireless Microphone	3
4	CAB-02	HDMI Cable (5 meters)	1
\.


--
-- Name: borrow_items_borrow_item_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.borrow_items_borrow_item_id_seq', 1, false);


--
-- Name: borrow_transactions_borrow_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.borrow_transactions_borrow_id_seq', 5, true);


--
-- Name: borrowers_borrower_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.borrowers_borrower_id_seq', 15, true);


--
-- Name: equipment_equipment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.equipment_equipment_id_seq', 5, true);


--
-- Name: borrow_items borrow_items_borrow_id_equipment_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.borrow_items
    ADD CONSTRAINT borrow_items_borrow_id_equipment_id_key UNIQUE (borrow_id, equipment_id);


--
-- Name: borrow_items borrow_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.borrow_items
    ADD CONSTRAINT borrow_items_pkey PRIMARY KEY (borrow_item_id);


--
-- Name: borrow_transactions borrow_transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.borrow_transactions
    ADD CONSTRAINT borrow_transactions_pkey PRIMARY KEY (borrow_id);


--
-- Name: borrowers borrowers_borrower_no_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.borrowers
    ADD CONSTRAINT borrowers_borrower_no_key UNIQUE (borrower_no);


--
-- Name: borrowers borrowers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.borrowers
    ADD CONSTRAINT borrowers_pkey PRIMARY KEY (borrower_id);


--
-- Name: equipment equipment_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipment
    ADD CONSTRAINT equipment_pkey PRIMARY KEY (equipment_id);


--
-- Name: equipment equipment_property_no_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipment
    ADD CONSTRAINT equipment_property_no_key UNIQUE (property_no);


--
-- Name: borrow_items borrow_items_borrow_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.borrow_items
    ADD CONSTRAINT borrow_items_borrow_id_fkey FOREIGN KEY (borrow_id) REFERENCES public.borrow_transactions(borrow_id) ON DELETE CASCADE;


--
-- Name: borrow_items borrow_items_equipment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.borrow_items
    ADD CONSTRAINT borrow_items_equipment_id_fkey FOREIGN KEY (equipment_id) REFERENCES public.equipment(equipment_id) ON DELETE RESTRICT;


--
-- Name: borrow_transactions borrow_transactions_borrower_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.borrow_transactions
    ADD CONSTRAINT borrow_transactions_borrower_id_fkey FOREIGN KEY (borrower_id) REFERENCES public.borrowers(borrower_id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict RlatAdjEDM7V7vhn1GgPM0jdryhi3lG5nojqRx1dvkMtifOyPZgkuELitNsn5Z8

