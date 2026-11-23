# Daily resources: instructions for the automation (Claude)

You add **one day of new TCF Canada study material** to the private study site, using only the API helper
`scripts/daily/api.sh` (secrets are in `~/.french-lms/config.env`; never print them, never commit them).
A few learners use the site, each on their own level (now all start at A1). They aim for **NCLC 7 (about CEFR B2)** in TCF Canada, studying about 2 hours a day.
The UI language is English with Hindi hints; the learning content is **French**.

Work in this folder: `D:\french-lms-laravel`. Do not change code or any other file, except the log (step 6).

## 1. Look at today

```bash
bash scripts/daily/api.sh GET /api/v1/plan/today
```

Read: `levels_needed` (every level that at least one learner is on today; usually one or two), and for each entry in
`levels`: `level`, `nclc`, `learners`, `day_numbers` and `already_added_today` (what is already done for that level today).
Also `writing_or_speaking` ("writing" or "speaking", the same for everyone today), `quota`, `recent_titles` and
`existing_words` (never repeat a topic or a word), `seed_grammar_slugs`.

**Repeat the whole job once for every level in `levels_needed`**, writing for that level. If everything in the quota is already
added today for a level, skip that level. If all levels are done, stop and log "already done".

## 2. What to create today (the quota)

| kind | how many | notes |
| --- | --- | --- |
| `vocab` | 1 set of **10 new words** | words not in `existing_words` (compare ignoring case, articles and accents); one theme (e.g. housing, work, health, shopping, government offices, weather, transport) |
| `grammar` | 1 short lesson | a topic not yet in `recent_titles` or the seed topics; small and practical; 3-quiz questions |
| `listening` | 1 | a short audio script (read aloud by the browser): announcement, voicemail, dialogue, radio snippet |
| `reading` | 1 | notice, ad, email, article extract |
| `writing` **or** `speaking` | 1 | the one named in `writing_or_speaking` (per level) |

Use Canada-relevant, everyday TCF themes: housing, jobs, public services, school, health, transport, weather, shopping,
Quebec and Ontario life, immigration paperwork. **All texts must be original** (no copied articles, no real people).

## 3. Level rules (write each set for its level in `levels_needed`; at most 20 percent of items may be one level higher)

| level | texts | grammar & style |
| --- | --- | --- |
| A1 | 40-70 words, one topic | present tense, être/avoir/aller/-er verbs, simple questions, numbers, short sentences |
| A2 | 80-130 words | passé composé, futur proche, pronouns, simple connectors (parce que, mais, alors) |
| B1 | 150-250 words | imparfait, conditionnel, opinions, si-clauses, longer connectors |
| B2 | 250-350 words | subjonctif, concession, argumentation, formal register; TCF-style tasks and distractors |

Writing tasks follow the TCF format: **tâche 1** short message (60-120 words), **tâche 2** article/blog (120-150),
**tâche 3** argumentative text with two viewpoints (120-180). Speaking follows: **tâche 1** guided interview,
**tâche 2** role-play with 2 min preparation, **tâche 3** express and defend an opinion. Pick the task number that fits the level
(A1/A2: tâche 1; B1: tâche 1-2; B2: tâche 2-3).

## 4. Quality rules (the French must be correct)

- Perfect spelling, accents, agreement, elision (l'école, j'habite) and punctuation (French spacing before `?` and `!` is fine either way, be consistent).
- Questions: 3 to 5 per text, **exactly one correct option**, options all different, plausible distractors, the correct index varies (not always 0).
- `explain` is a short English sentence that points to the sentence in the text or the grammar rule.
- Give an English translation (`en`) and, when easy, Hindi (`hi`, Devanagari). Hindi is optional; skip it rather than write it badly.
- Vocabulary: include IPA, part of speech (`nom m`, `nom f`, `verbe`, `adj`, ...), one example sentence at the learner's level with English translation.
- Do not include real phone numbers, emails or addresses (use 555 numbers, example names).
- If you are unsure a sentence is correct French, rewrite it simpler.

## 5. Create the items and post them

Write each item as a JSON file in a scratch folder (anywhere outside the repo, e.g. `~/.french-lms/work/`), then:

```bash
bash scripts/daily/api.sh POST /api/v1/materials ~/.french-lms/work/<file>.json
```

Success is HTTP 201 (created) or 200 (updated). On HTTP 422 read the `error` text, fix the JSON, post again (max 2 retries per item).
Give every item a stable **slug** like `a1-d003-reading-le-marche` (lowercase letters, digits and hyphens;
level + day number + kind + short topic; use the first number in that level's `day_numbers`, or today's date as `0123` for MMDD when you prefer). Do **not** set `publish_on` (it defaults to today).

### Schemas

Common fields for every item: `kind`, `level` (A1|A2|B1|B2|C1), `title` (French or English, short), optional `title_hi`,
`summary` (one English line), `minutes` (5-30), optional `nclc`, `slug`, and `body` (below).

**vocab** (stored in the flashcard deck, not as a lesson page)
```json
{"kind":"vocab","level":"A1","title":"Se présenter","body":{"words":[
 {"fr":"le prénom","ipa":"/pʁe.nɔ̃/","pos":"nom m","en":"first name","hi":"पहला नाम",
  "example_fr":"Mon prénom est Anna.","example_en":"My first name is Anna.","example_hi":"मेरा पहला नाम अन्ना है।","category":"Introductions"}
]}}
```
Exactly 10 words (min 5). `fr`, `en`, `example_fr`, `example_en` are required.
The response lists `skipped` words that already exist in the core list (they are left unchanged): post a second small set with
**replacement words** until 10 new words were added today (`added` + `updated` = 10 across your posts).

**reading** and **listening** (listening script is read aloud by text-to-speech; write it as natural spoken French)
```json
{"kind":"reading","level":"A1","title":"Une carte de présentation","summary":"...","minutes":10,
 "body":{"fr":"...text...","en":"...translation...","hi":"(optional)",
  "glossary":[{"fr":"travailler","en":"to work","hi":"काम करना"}],
  "questions":[{"q":"Quel âge a Sofia ?","options":["20 ans","23 ans","32 ans"],"correct":1,"explain":"« J'ai vingt-trois ans »."}]}}
```
For dialogues put each speaker on its own line inside `fr` (`Madame Lefèvre : ...`).

**grammar**
```json
{"kind":"grammar","level":"A1","title":"Tu ou vous ?","title_hi":"तू या आप?","summary":"...","minutes":15,
 "body":{"summary_hi":"(optional)",
  "rules":[{"rule":"Use « tu » with friends...","rule_hi":"(optional)",
            "examples":[{"fr":"Tu t'appelles comment ?","en":"What is your name? (informal)","hi":"(optional)"},{"fr":"...","en":"..."}]}],
  "tables":[{"caption":"être","headers":["pronom","forme"],"rows":[["je","suis"],["tu","es"]]}],
  "tips":["Short practical tip."],
  "quiz":[{"q":"...","options":["a","b","c"],"correct":1,"explain":"..."}]}}
```
At least 1 rule with at least 2 examples; at least 3 quiz questions.

**writing**
```json
{"kind":"writing","level":"A2","title":"Message à un voisin","summary":"TCF tâche 1 · 60+ words","minutes":20,
 "body":{"exam":"TCF","prompt":"Vous écrivez un message à votre voisin...","prompt_hi":"(optional)","min_words":60,
  "model_fr":"...a good model answer at the level...","model_en":"...translation...",
  "checklist":["Greeting and closing","Answers every point of the task","Right verb tense","At least 60 words"],
  "useful_phrases":[{"fr":"Je vous écris pour...","en":"I am writing to you to..."}]}}
```

**speaking**
```json
{"kind":"speaking","level":"A1","title":"Entretien dirigé : se présenter","summary":"TCF tâche 1 · speak 2 min","minutes":10,
 "body":{"exam":"TCF","prompt":"Présentez-vous en 6 à 8 phrases...","prompt_hi":"(optional)","speak_minutes":2,
  "phrases":[{"fr":"Je m'appelle...","en":"My name is...","hi":"मेरा नाम... है"}],
  "model_fr":"...a model answer the learner can read after trying..."}}
```
At least 3 phrases.

## 6. Verify and log

```bash
bash scripts/daily/api.sh GET /api/v1/plan/today
```
For every level you worked on, check `already_added_today` now matches the quota. Then append one line to `scripts/daily/log.md` (git-ignored):
`YYYY-MM-DD · level X (n learners) · added: vocab 10, grammar 1, listening 1, reading 1, speaking 1 · problems: none` (one line per level).
Finish with a 3-line summary of what was added (titles) and anything that failed. Never leave half-created work silently: if the
API is unreachable (HTTP error, timeout, 401), say so clearly and stop.
