---
name: hermes
description: Autonomous ingestion and monitoring agent for Académie de Français LMS. Use to ingest classical vocabulary with Hindi translations, query lexicon, or check LMS health.
---

# Hermes Autonomous Agent Skill

This skill allows the Antigravity agent to interact with the Académie de Français LMS using the lightweight Hermes CLI (`python ../hermes.py`).

## Core Capabilities
- **Status Probe**: Check live LMS statistics (courses, lessons, scholars, vocabulary).
  `python ../hermes.py status`
- **Daily Dispatch**: Ingest the next scheduled classical French term with Devanagari Hindi meaning (`meaning_hi`) and practical usage context (`use_case`).
  `python ../hermes.py daily`
- **Ingest Term**: Ingest custom classical words.
  `python ../hermes.py ingest --word "Mot" --en "English" --hi "हिन्दी" --use-case "Context"`
- **List Lexicon**: Filter and list vocabulary from the sanctuary.
  `python ../hermes.py list --level B2`
