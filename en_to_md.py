#!/usr/bin/python
# -*- coding: utf-8 -*-
"""
11p deletion syndrome
Vulvar pain
Diabetic foot infection
Kidney agenesis
Orbital compartment syndrome
Pelvic floor disorders
Perianal itching
Genital itch
Lateral canthotomy
"""
#
# (C) Ibrahem Qasim, 2021
#
#
import codecs
import sys
import json
#===============================
sys_argv = sys.argv or []
#===============================
# Etonogestrel موجودة في ويكي إنجليزية تحويلة إلى المقالة الهدف الموجودة في ويكي ميد
#===============================
enwiki_to_mdwiki = {
    #"Pubic lice" : "Crab louse",
    #"Heart tumor" : "Primary tumors of the heart",
    #"Hantavirus infection" : "Orthohantavirus",
    #"Esophageal balloon tamponade" : "Balloon tamponade",
    #"Hemorrhagic shock" : "Hypovolemia",
    #"Prostate abscess" : "Acute prostatitis",   
    
    "Etonogestrel" : "Etonogestrel birth control implant",
    
    
    "Transitional cell carcinoma" : "Urothelial carcinoma",
    "Shaken baby syndrome" : "Abusive head trauma",
    "Dysautonomia" : "Autonomic dysfunction",
    "Barrett's esophagus":"Barrett esophagus",
    "Beau's lines":"Beau lines",
    "Nocturnal enuresis":"Bedwetting",
    "Beta blocker":"Beta blockers",
    "Trepanning":"Burr hole",
    "Intracranial aneurysm":"Cerebral aneurysm",
    "Spasmodic torticollis":"Cervical dystonia",
    "Cocaine dependence":"Cocaine use disorder",
    "Rapidly progressive glomerulonephritis":"Crescentic glomerulonephritis",
    "Diabetic nephropathy":"Diabetic kidney disease",
    "Irritant diaper dermatitis":"Diaper dermatitis",
    "Seizure":"Epileptic seizure",
    "Ewing's sarcoma":"Ewing sarcoma",
    "Scopolamine":"Hyoscine",
    "Hypocalcemia":"Hypocalcaemia",
    "Ipratropium bromide/salbutamol":"Ipratropium/salbutamol",
    "Multicystic dysplastic kidney":"Kidney dysplasia",
    "Anosmia":"Loss of smell",
    "Membranous glomerulonephritis":"Membranous nephropathy",
    "Neurogenic bladder dysfunction":"Neurogenic bladder",
    "Dysthymia":"Persistent depressive disorder",
    "Petechia":"Petechiae",
    "Pulmonary insufficiency":"Pulmonary valve insufficiency",
    "Renal cell carcinoma":"Renal cell cancer",
    "Suprapubic cystostomy":"Suprapubic catheter",
    "Nicotine dependence":"Tobacco use disorder",
    "Transposition of the great vessels":"Transposition of the great arteries",
    "Trisomy X":"Triple X syndrome",
    "Cryptorchidism":"Undescended testis",

    }
#===============================
mdwiki_to_enwiki = {}
for ene,mde in enwiki_to_mdwiki.items():
    mdwiki_to_enwiki[mde] = ene
#===============================
lala = ''
From_json = {}
#===============================
project = '/mnt/nfs/labstore-secondary-tools-project/'
#-------------------------------
if u'local' in sys_argv: project = '/master/'
#-------------------------------
ffile = project + 'mdwiki/public_html/Translation_Dashboard/Tables/medwiki_to_enwiki.json'
#===============================
with codecs.open( ffile , "r", encoding="utf-8-sig") as listt:
    lala = listt.read()
listt.close()
#===============================
fa = str(lala)
#===============================
if fa != '' : 
    From_json = json.loads(fa)
#===============================
for md,en in From_json.items():
    enwiki_to_mdwiki[en] = md
    #------------------------
    mdwiki_to_enwiki[md] = en
#===============================





#===============================