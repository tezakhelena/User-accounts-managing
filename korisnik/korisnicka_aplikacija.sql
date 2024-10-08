PGDMP       
                 |            korisnicka_aplikacija    16.1    16.1 N    F           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                      false            G           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                      false            H           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                      false            I           1262    16560    korisnicka_aplikacija    DATABASE     �   CREATE DATABASE korisnicka_aplikacija WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Croatian_Croatia.1250';
 %   DROP DATABASE korisnicka_aplikacija;
                postgres    false            �            1255    24954    after_insert_postovi()    FUNCTION     -  CREATE FUNCTION public.after_insert_postovi() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  post_author_id INT;
  user_id INT;
BEGIN
  post_author_id := NEW.korisnik_id;

  -- Dohvati sve korisnike
  FOR user_id IN SELECT id FROM korisnici
  LOOP
    -- Preskoči slanje notifikacije stvaratelju posta
    IF user_id <> post_author_id THEN
      -- Pošalji notifikaciju svim korisnicima
      INSERT INTO notifikacije (korisnik_id, sadrzaj)
      VALUES (user_id, 'Dodan je novi post na forum.');
    END IF;
  END LOOP;

  RETURN NEW;
END;
$$;
 -   DROP FUNCTION public.after_insert_postovi();
       public          postgres    false            �            1255    24958 "   after_update_aktivnost_korisnici()    FUNCTION     �  CREATE FUNCTION public.after_update_aktivnost_korisnici() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  IF NEW.aktivan <> OLD.aktivan THEN
    INSERT INTO notifikacije (korisnik_id, sadrzaj, procitano)
    VALUES (NEW.id, CASE WHEN NEW.aktivan THEN 'Vaš korisnički račun je ponovno aktivan.' ELSE 'Vaš korisnički račun je deaktiviran.' END, FALSE);
  END IF;
  RETURN NEW;
END;
$$;
 9   DROP FUNCTION public.after_update_aktivnost_korisnici();
       public          postgres    false            �            1255    16606    dob_korisnika(date)    FUNCTION     �   CREATE FUNCTION public.dob_korisnika(rodjendan date) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN EXTRACT(YEAR FROM CURRENT_DATE) - EXTRACT(YEAR FROM rodjendan);
END;
$$;
 4   DROP FUNCTION public.dob_korisnika(rodjendan date);
       public          postgres    false            �            1255    24963    dodajnotifikacijunakomentar()    FUNCTION       CREATE FUNCTION public.dodajnotifikacijunakomentar() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    post_korisnik_id INT;
    korisnik_ime VARCHAR(255);
BEGIN
    SELECT korisnik_id INTO post_korisnik_id
    FROM postovi
    WHERE post_id = NEW.post_id;

    SELECT korisnicko_ime INTO korisnik_ime
    FROM korisnici
    WHERE id = NEW.korisnik_id;
    
    INSERT INTO notifikacije (korisnik_id, sadrzaj)
    VALUES (post_korisnik_id, korisnik_ime || ' je ostavio komentar na vaš post');
    
    RETURN NEW;
END;
$$;
 4   DROP FUNCTION public.dodajnotifikacijunakomentar();
       public          postgres    false            �            1255    24965    dodajnotifikacijunaodgovor()    FUNCTION     �  CREATE FUNCTION public.dodajnotifikacijunaodgovor() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    komentar_korisnik_id INT;
    korisnik_ime VARCHAR(255);
BEGIN
    -- Dohvati korisnika koji je ostavio komentar na koji je odgovoreno
    SELECT korisnik_id INTO komentar_korisnik_id
    FROM komentari
    WHERE komentar_id = NEW.komentar_id; -- Promijenjeno ovdje

    SELECT korisnicko_ime INTO korisnik_ime
    FROM korisnici
    WHERE id = NEW.korisnik_id;
    
    INSERT INTO notifikacije (korisnik_id, sadrzaj)
    VALUES (komentar_korisnik_id, korisnik_ime || ' je ostavio odgovor na vaš komentar');
    
    RETURN NEW;
END;
$$;
 3   DROP FUNCTION public.dodajnotifikacijunaodgovor();
       public          postgres    false            �            1255    16607 >   generiraj_korisnicko_ime(character varying, character varying)    FUNCTION     �   CREATE FUNCTION public.generiraj_korisnicko_ime(ime character varying, prezime character varying) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN LOWER(SUBSTRING(ime FROM 1 FOR 1) || prezime);
END;
$$;
 a   DROP FUNCTION public.generiraj_korisnicko_ime(ime character varying, prezime character varying);
       public          postgres    false            �            1255    24801    is_admin(integer)    FUNCTION     �   CREATE FUNCTION public.is_admin(vrsta_korisnika_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN vrsta_korisnika_id = 1;
END;
$$;
 ;   DROP FUNCTION public.is_admin(vrsta_korisnika_id integer);
       public          postgres    false            �            1255    24977    obrisi_povezane_podatke()    FUNCTION     �  CREATE FUNCTION public.obrisi_povezane_podatke() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Brisanje notifikacija povezanih s korisnikom
    DELETE FROM notifikacije WHERE korisnik_id = OLD.id;

    -- Brisanje postova povezanih s korisnikom
    DELETE FROM postovi WHERE korisnik_id = OLD.id;

    -- Brisanje profila korisnika povezanog s korisnikom
    DELETE FROM profili_korisnika WHERE korisnik_id = OLD.id;

    -- Brisanje komentara povezanih s korisnikom
    DELETE FROM komentari WHERE korisnik_id = OLD.id;

    -- Brisanje odgovora povezanih s korisnikom
    DELETE FROM odgovori WHERE korisnik_id = OLD.id;

    RETURN OLD;
END;
$$;
 0   DROP FUNCTION public.obrisi_povezane_podatke();
       public          postgres    false            �            1255    16610 /   pretvori_u_samo_datum(timestamp with time zone)    FUNCTION     �   CREATE FUNCTION public.pretvori_u_samo_datum(ts timestamp with time zone) RETURNS date
    LANGUAGE sql
    AS $$
    SELECT DATE(ts);
$$;
 I   DROP FUNCTION public.pretvori_u_samo_datum(ts timestamp with time zone);
       public          postgres    false            �            1255    16611    prikazi_hobije(text[])    FUNCTION     "  CREATE FUNCTION public.prikazi_hobije(omiljeni_hobiji text[]) RETURNS text
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF array_length(omiljeni_hobiji, 1) IS NULL THEN
        RETURN 'Nema omiljenih hobija';
    ELSE
        RETURN array_to_string(omiljeni_hobiji, ', ');
    END IF;
END;
$$;
 =   DROP FUNCTION public.prikazi_hobije(omiljeni_hobiji text[]);
       public          postgres    false            �            1259    24832 	   komentari    TABLE     �   CREATE TABLE public.komentari (
    komentar_id integer NOT NULL,
    post_id integer NOT NULL,
    korisnik_id integer NOT NULL,
    sadrzaj character varying(50),
    datum_komentara date DEFAULT now()
);
    DROP TABLE public.komentari;
       public         heap    postgres    false            �            1259    24903    komentari_id_seq    SEQUENCE     y   CREATE SEQUENCE public.komentari_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 '   DROP SEQUENCE public.komentari_id_seq;
       public          postgres    false    222            J           0    0    komentari_id_seq    SEQUENCE OWNED BY     N   ALTER SEQUENCE public.komentari_id_seq OWNED BY public.komentari.komentar_id;
          public          postgres    false    225            �            1259    16571 	   korisnici    TABLE     �  CREATE TABLE public.korisnici (
    id integer NOT NULL,
    korisnicko_ime character varying(50) NOT NULL,
    lozinka character varying(100) NOT NULL,
    vrsta_korisnika_id integer,
    ime character varying(50),
    prezime character varying(50),
    email character varying(100),
    datum_registracije timestamp with time zone DEFAULT now(),
    aktivan boolean DEFAULT true,
    omiljeni_hobi character varying(50)[]
);
    DROP TABLE public.korisnici;
       public         heap    postgres    false            �            1259    16570    korisnici_id_seq    SEQUENCE     �   CREATE SEQUENCE public.korisnici_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 '   DROP SEQUENCE public.korisnici_id_seq;
       public          postgres    false    218            K           0    0    korisnici_id_seq    SEQUENCE OWNED BY     E   ALTER SEQUENCE public.korisnici_id_seq OWNED BY public.korisnici.id;
          public          postgres    false    217            �            1259    24935    notifikacije    TABLE     �   CREATE TABLE public.notifikacije (
    korisnik_id integer NOT NULL,
    notifikacija_id integer NOT NULL,
    sadrzaj character varying,
    procitano boolean DEFAULT false
);
     DROP TABLE public.notifikacije;
       public         heap    postgres    false            �            1259    24961     notifikacije_notifikacija_id_seq    SEQUENCE     �   ALTER TABLE public.notifikacije ALTER COLUMN notifikacija_id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.notifikacije_notifikacija_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);
            public          postgres    false    227            �            1259    24847    odgovori    TABLE     �   CREATE TABLE public.odgovori (
    odgovor_id integer NOT NULL,
    komentar_id integer NOT NULL,
    korisnik_id integer NOT NULL,
    sadrzaj character varying(50),
    datum_odgovora date DEFAULT now()
);
    DROP TABLE public.odgovori;
       public         heap    postgres    false            �            1259    24917    odgovori_id_seq    SEQUENCE     x   CREATE SEQUENCE public.odgovori_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 &   DROP SEQUENCE public.odgovori_id_seq;
       public          postgres    false    223            L           0    0    odgovori_id_seq    SEQUENCE OWNED BY     K   ALTER SEQUENCE public.odgovori_id_seq OWNED BY public.odgovori.odgovor_id;
          public          postgres    false    226            �            1259    24807    postovi    TABLE     �   CREATE TABLE public.postovi (
    post_id integer NOT NULL,
    korisnik_id integer NOT NULL,
    naslov character varying,
    sadrzaj character varying,
    datum_objave timestamp without time zone DEFAULT now()
);
    DROP TABLE public.postovi;
       public         heap    postgres    false            �            1259    24862    postovi_post_id_seq    SEQUENCE     |   CREATE SEQUENCE public.postovi_post_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 *   DROP SEQUENCE public.postovi_post_id_seq;
       public          postgres    false    221            M           0    0    postovi_post_id_seq    SEQUENCE OWNED BY     K   ALTER SEQUENCE public.postovi_post_id_seq OWNED BY public.postovi.post_id;
          public          postgres    false    224            �            1259    16589    profili_korisnika    TABLE     �   CREATE TABLE public.profili_korisnika (
    id integer NOT NULL,
    korisnik_id integer,
    adresa character varying(255),
    broj_telefona character varying(20),
    rodjendan date,
    spol character varying(10),
    biografske_informacije text
);
 %   DROP TABLE public.profili_korisnika;
       public         heap    postgres    false            �            1259    16588    profili_korisnika_id_seq    SEQUENCE     �   CREATE SEQUENCE public.profili_korisnika_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 /   DROP SEQUENCE public.profili_korisnika_id_seq;
       public          postgres    false    220            N           0    0    profili_korisnika_id_seq    SEQUENCE OWNED BY     U   ALTER SEQUENCE public.profili_korisnika_id_seq OWNED BY public.profili_korisnika.id;
          public          postgres    false    219            �            1259    16562    vrste_korisnika    TABLE     k   CREATE TABLE public.vrste_korisnika (
    id integer NOT NULL,
    naziv character varying(50) NOT NULL
);
 #   DROP TABLE public.vrste_korisnika;
       public         heap    postgres    false            �            1259    16561    vrste_korisnika_id_seq    SEQUENCE     �   CREATE SEQUENCE public.vrste_korisnika_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 -   DROP SEQUENCE public.vrste_korisnika_id_seq;
       public          postgres    false    216            O           0    0    vrste_korisnika_id_seq    SEQUENCE OWNED BY     Q   ALTER SEQUENCE public.vrste_korisnika_id_seq OWNED BY public.vrste_korisnika.id;
          public          postgres    false    215                       2604    24911    komentari komentar_id    DEFAULT     u   ALTER TABLE ONLY public.komentari ALTER COLUMN komentar_id SET DEFAULT nextval('public.komentari_id_seq'::regclass);
 D   ALTER TABLE public.komentari ALTER COLUMN komentar_id DROP DEFAULT;
       public          postgres    false    225    222            y           2604    16574    korisnici id    DEFAULT     l   ALTER TABLE ONLY public.korisnici ALTER COLUMN id SET DEFAULT nextval('public.korisnici_id_seq'::regclass);
 ;   ALTER TABLE public.korisnici ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    217    218    218            �           2604    24920    odgovori odgovor_id    DEFAULT     r   ALTER TABLE ONLY public.odgovori ALTER COLUMN odgovor_id SET DEFAULT nextval('public.odgovori_id_seq'::regclass);
 B   ALTER TABLE public.odgovori ALTER COLUMN odgovor_id DROP DEFAULT;
       public          postgres    false    226    223            }           2604    24870    postovi post_id    DEFAULT     r   ALTER TABLE ONLY public.postovi ALTER COLUMN post_id SET DEFAULT nextval('public.postovi_post_id_seq'::regclass);
 >   ALTER TABLE public.postovi ALTER COLUMN post_id DROP DEFAULT;
       public          postgres    false    224    221            |           2604    16592    profili_korisnika id    DEFAULT     |   ALTER TABLE ONLY public.profili_korisnika ALTER COLUMN id SET DEFAULT nextval('public.profili_korisnika_id_seq'::regclass);
 C   ALTER TABLE public.profili_korisnika ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    219    220    220            x           2604    16565    vrste_korisnika id    DEFAULT     x   ALTER TABLE ONLY public.vrste_korisnika ALTER COLUMN id SET DEFAULT nextval('public.vrste_korisnika_id_seq'::regclass);
 A   ALTER TABLE public.vrste_korisnika ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    216    215    216            =          0    24832 	   komentari 
   TABLE DATA           `   COPY public.komentari (komentar_id, post_id, korisnik_id, sadrzaj, datum_komentara) FROM stdin;
    public          postgres    false    222   �j       9          0    16571 	   korisnici 
   TABLE DATA           �   COPY public.korisnici (id, korisnicko_ime, lozinka, vrsta_korisnika_id, ime, prezime, email, datum_registracije, aktivan, omiljeni_hobi) FROM stdin;
    public          postgres    false    218   �j       B          0    24935    notifikacije 
   TABLE DATA           X   COPY public.notifikacije (korisnik_id, notifikacija_id, sadrzaj, procitano) FROM stdin;
    public          postgres    false    227   1l       >          0    24847    odgovori 
   TABLE DATA           a   COPY public.odgovori (odgovor_id, komentar_id, korisnik_id, sadrzaj, datum_odgovora) FROM stdin;
    public          postgres    false    223   �l       <          0    24807    postovi 
   TABLE DATA           V   COPY public.postovi (post_id, korisnik_id, naslov, sadrzaj, datum_objave) FROM stdin;
    public          postgres    false    221   5m       ;          0    16589    profili_korisnika 
   TABLE DATA           |   COPY public.profili_korisnika (id, korisnik_id, adresa, broj_telefona, rodjendan, spol, biografske_informacije) FROM stdin;
    public          postgres    false    220   zm       7          0    16562    vrste_korisnika 
   TABLE DATA           4   COPY public.vrste_korisnika (id, naziv) FROM stdin;
    public          postgres    false    216   n       P           0    0    komentari_id_seq    SEQUENCE SET     ?   SELECT pg_catalog.setval('public.komentari_id_seq', 64, true);
          public          postgres    false    225            Q           0    0    korisnici_id_seq    SEQUENCE SET     ?   SELECT pg_catalog.setval('public.korisnici_id_seq', 26, true);
          public          postgres    false    217            R           0    0     notifikacije_notifikacija_id_seq    SEQUENCE SET     O   SELECT pg_catalog.setval('public.notifikacije_notifikacija_id_seq', 59, true);
          public          postgres    false    228            S           0    0    odgovori_id_seq    SEQUENCE SET     >   SELECT pg_catalog.setval('public.odgovori_id_seq', 67, true);
          public          postgres    false    226            T           0    0    postovi_post_id_seq    SEQUENCE SET     B   SELECT pg_catalog.setval('public.postovi_post_id_seq', 10, true);
          public          postgres    false    224            U           0    0    profili_korisnika_id_seq    SEQUENCE SET     G   SELECT pg_catalog.setval('public.profili_korisnika_id_seq', 16, true);
          public          postgres    false    219            V           0    0    vrste_korisnika_id_seq    SEQUENCE SET     D   SELECT pg_catalog.setval('public.vrste_korisnika_id_seq', 3, true);
          public          postgres    false    215            �           2606    24905    komentari komentari_pkey 
   CONSTRAINT     _   ALTER TABLE ONLY public.komentari
    ADD CONSTRAINT komentari_pkey PRIMARY KEY (komentar_id);
 B   ALTER TABLE ONLY public.komentari DROP CONSTRAINT komentari_pkey;
       public            postgres    false    222            �           2606    16582    korisnici korisnici_email_key 
   CONSTRAINT     Y   ALTER TABLE ONLY public.korisnici
    ADD CONSTRAINT korisnici_email_key UNIQUE (email);
 G   ALTER TABLE ONLY public.korisnici DROP CONSTRAINT korisnici_email_key;
       public            postgres    false    218            �           2606    16580 &   korisnici korisnici_korisnicko_ime_key 
   CONSTRAINT     k   ALTER TABLE ONLY public.korisnici
    ADD CONSTRAINT korisnici_korisnicko_ime_key UNIQUE (korisnicko_ime);
 P   ALTER TABLE ONLY public.korisnici DROP CONSTRAINT korisnici_korisnicko_ime_key;
       public            postgres    false    218            �           2606    16578    korisnici korisnici_pkey 
   CONSTRAINT     V   ALTER TABLE ONLY public.korisnici
    ADD CONSTRAINT korisnici_pkey PRIMARY KEY (id);
 B   ALTER TABLE ONLY public.korisnici DROP CONSTRAINT korisnici_pkey;
       public            postgres    false    218            �           2606    24941    notifikacije notifikacije_pkey 
   CONSTRAINT     i   ALTER TABLE ONLY public.notifikacije
    ADD CONSTRAINT notifikacije_pkey PRIMARY KEY (notifikacija_id);
 H   ALTER TABLE ONLY public.notifikacije DROP CONSTRAINT notifikacije_pkey;
       public            postgres    false    227            �           2606    24919    odgovori odgovori_pkey 
   CONSTRAINT     \   ALTER TABLE ONLY public.odgovori
    ADD CONSTRAINT odgovori_pkey PRIMARY KEY (odgovor_id);
 @   ALTER TABLE ONLY public.odgovori DROP CONSTRAINT odgovori_pkey;
       public            postgres    false    223            �           2606    24864    postovi postovi_pkey 
   CONSTRAINT     W   ALTER TABLE ONLY public.postovi
    ADD CONSTRAINT postovi_pkey PRIMARY KEY (post_id);
 >   ALTER TABLE ONLY public.postovi DROP CONSTRAINT postovi_pkey;
       public            postgres    false    221            �           2606    16598 3   profili_korisnika profili_korisnika_korisnik_id_key 
   CONSTRAINT     u   ALTER TABLE ONLY public.profili_korisnika
    ADD CONSTRAINT profili_korisnika_korisnik_id_key UNIQUE (korisnik_id);
 ]   ALTER TABLE ONLY public.profili_korisnika DROP CONSTRAINT profili_korisnika_korisnik_id_key;
       public            postgres    false    220            �           2606    16596 (   profili_korisnika profili_korisnika_pkey 
   CONSTRAINT     f   ALTER TABLE ONLY public.profili_korisnika
    ADD CONSTRAINT profili_korisnika_pkey PRIMARY KEY (id);
 R   ALTER TABLE ONLY public.profili_korisnika DROP CONSTRAINT profili_korisnika_pkey;
       public            postgres    false    220            �           2606    16569 )   vrste_korisnika vrste_korisnika_naziv_key 
   CONSTRAINT     e   ALTER TABLE ONLY public.vrste_korisnika
    ADD CONSTRAINT vrste_korisnika_naziv_key UNIQUE (naziv);
 S   ALTER TABLE ONLY public.vrste_korisnika DROP CONSTRAINT vrste_korisnika_naziv_key;
       public            postgres    false    216            �           2606    16567 $   vrste_korisnika vrste_korisnika_pkey 
   CONSTRAINT     b   ALTER TABLE ONLY public.vrste_korisnika
    ADD CONSTRAINT vrste_korisnika_pkey PRIMARY KEY (id);
 N   ALTER TABLE ONLY public.vrste_korisnika DROP CONSTRAINT vrste_korisnika_pkey;
       public            postgres    false    216            �           2620    24955    postovi after_insert_postovi    TRIGGER     �   CREATE TRIGGER after_insert_postovi AFTER INSERT ON public.postovi FOR EACH ROW EXECUTE FUNCTION public.after_insert_postovi();
 5   DROP TRIGGER after_insert_postovi ON public.postovi;
       public          postgres    false    221    235            �           2620    24960 *   korisnici after_update_aktivnost_korisnici    TRIGGER     �   CREATE TRIGGER after_update_aktivnost_korisnici AFTER UPDATE ON public.korisnici FOR EACH ROW EXECUTE FUNCTION public.after_update_aktivnost_korisnici();
 C   DROP TRIGGER after_update_aktivnost_korisnici ON public.korisnici;
       public          postgres    false    218    234            �           2620    24964     komentari trg_dodaj_notifikaciju    TRIGGER     �   CREATE TRIGGER trg_dodaj_notifikaciju AFTER INSERT ON public.komentari FOR EACH ROW EXECUTE FUNCTION public.dodajnotifikacijunakomentar();
 9   DROP TRIGGER trg_dodaj_notifikaciju ON public.komentari;
       public          postgres    false    248    222            �           2620    24967 '   odgovori trg_dodaj_notifikaciju_odgovor    TRIGGER     �   CREATE TRIGGER trg_dodaj_notifikaciju_odgovor AFTER INSERT ON public.odgovori FOR EACH ROW EXECUTE FUNCTION public.dodajnotifikacijunaodgovor();
 @   DROP TRIGGER trg_dodaj_notifikaciju_odgovor ON public.odgovori;
       public          postgres    false    247    223            �           2620    24978 %   korisnici trg_obrisi_povezane_podatke    TRIGGER     �   CREATE TRIGGER trg_obrisi_povezane_podatke BEFORE DELETE ON public.korisnici FOR EACH ROW EXECUTE FUNCTION public.obrisi_povezane_podatke();
 >   DROP TRIGGER trg_obrisi_povezane_podatke ON public.korisnici;
       public          postgres    false    218    249            �           2606    24906    odgovori komentari    FK CONSTRAINT     �   ALTER TABLE ONLY public.odgovori
    ADD CONSTRAINT komentari FOREIGN KEY (komentar_id) REFERENCES public.komentari(komentar_id);
 <   ALTER TABLE ONLY public.odgovori DROP CONSTRAINT komentari;
       public          postgres    false    222    4757    223            �           2606    24812    postovi korisnici    FK CONSTRAINT     x   ALTER TABLE ONLY public.postovi
    ADD CONSTRAINT korisnici FOREIGN KEY (korisnik_id) REFERENCES public.korisnici(id);
 ;   ALTER TABLE ONLY public.postovi DROP CONSTRAINT korisnici;
       public          postgres    false    221    4749    218            �           2606    24837    komentari korisnici    FK CONSTRAINT     z   ALTER TABLE ONLY public.komentari
    ADD CONSTRAINT korisnici FOREIGN KEY (korisnik_id) REFERENCES public.korisnici(id);
 =   ALTER TABLE ONLY public.komentari DROP CONSTRAINT korisnici;
       public          postgres    false    4749    222    218            �           2606    24852    odgovori korisnici    FK CONSTRAINT     y   ALTER TABLE ONLY public.odgovori
    ADD CONSTRAINT korisnici FOREIGN KEY (korisnik_id) REFERENCES public.korisnici(id);
 <   ALTER TABLE ONLY public.odgovori DROP CONSTRAINT korisnici;
       public          postgres    false    4749    218    223            �           2606    16583 +   korisnici korisnici_vrsta_korisnika_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.korisnici
    ADD CONSTRAINT korisnici_vrsta_korisnika_id_fkey FOREIGN KEY (vrsta_korisnika_id) REFERENCES public.vrste_korisnika(id);
 U   ALTER TABLE ONLY public.korisnici DROP CONSTRAINT korisnici_vrsta_korisnika_id_fkey;
       public          postgres    false    216    4743    218            �           2606    24942    notifikacije korisnik    FK CONSTRAINT     |   ALTER TABLE ONLY public.notifikacije
    ADD CONSTRAINT korisnik FOREIGN KEY (korisnik_id) REFERENCES public.korisnici(id);
 ?   ALTER TABLE ONLY public.notifikacije DROP CONSTRAINT korisnik;
       public          postgres    false    4749    218    227            �           2606    24865    komentari postovi    FK CONSTRAINT     w   ALTER TABLE ONLY public.komentari
    ADD CONSTRAINT postovi FOREIGN KEY (post_id) REFERENCES public.postovi(post_id);
 ;   ALTER TABLE ONLY public.komentari DROP CONSTRAINT postovi;
       public          postgres    false    222    4755    221            �           2606    16599 4   profili_korisnika profili_korisnika_korisnik_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.profili_korisnika
    ADD CONSTRAINT profili_korisnika_korisnik_id_fkey FOREIGN KEY (korisnik_id) REFERENCES public.korisnici(id);
 ^   ALTER TABLE ONLY public.profili_korisnika DROP CONSTRAINT profili_korisnika_korisnik_id_fkey;
       public          postgres    false    4749    218    220            =   ;   x�33�4�42��K��T���M�+I,�4202�50�52�25 )��HE@���%W� Z��      9   K  x�m�KR�@����,�)q�'䵒�KC ��$0@�̄TP���3D,7l����_�ě�� EL�@���p0�>����zl���M'�Y+ugs,�ɪ/��ӍqZ�?�	�J��{���6�T�)j%
%�`�Բ5]�M�uC�$�xRP'Q��`������k-��h�V�����݃�4p����<4������o�y�#H\��dR��ܼ��P@���h���i^J��!O� �,�|���&�Xxz"?��*:�YO��N��lau�\Ԛ�~�_=W�.3;U�%�ό�{2-W��}=-�2�i*eXxn��c���ڻ}���<��'�]U�叄*      B   �   x���A�0�u{��� R��gp�f"�T�)�����%-Q7����73��`�d'x�EKF�����<�(nJ���V;��5<_L���*����1}V� �Ň4Xp��Wrd�p�N�ìU$�i,�<�e�M��Q�$d);R�)��uH|�!3v�:�`}�L�rC|?B�c�g���9���      >   )   x�33�45�42��OI�/�/�4202�50�52����� |��      <   5   x�3�4�,N+NIi))i�i)i�@&q���Z(X�W� )�      ;   |   x�34�42���LNT��,�/�I,KT�.�I�JU0�4��0735162�4204�50�54�<�75�8;��7?+Q!)3?�(1-3+��Ќ��bP����H/P��!X��o�х@�~��(c���� ¸'2      7   (   x�3�tL����,.)J,�/�2���/�,�������� �q	�     