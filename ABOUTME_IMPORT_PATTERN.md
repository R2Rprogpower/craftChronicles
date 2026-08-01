# AboutMe Import Pattern

This file is a ready-to-use text bundle template for the religion bundle import flow.

Use it in:
- `/admin/religions/commands/link`
- `Bundle Import / Export (TXT)` card
- paste into the import textarea and submit

## Required Sections

The import format is sectioned plain text with these blocks:

- `[RELIGION]`
- `[CONFESSION]`
- `[LANGUAGES]`
- `[RITUALS]`
- `[REMINDERS]`
- `[WORDS]`

## Full Template

```txt
# Example import bundle
# Lines starting with # are ignored

[RELIGION]
name: Ghuzel
slug: ghuzel
description: Ghuzelism is a warm household religion of tiny reasonable creatures called ghuzliks.
description_format: plain
is_active: 1

[CONFESSION]
name: Original
slug: original
description: Original confession of Ghuzel.
description_format: plain
welcome_message: Welcome to Ghuzel original confession
language_code: en
is_active: 1

[LANGUAGES]
code: en
name: English
native_name: English
script: Latin
words_json: [{"word":"shalom","translation":"peace","transcription":"sha-lom","phrases":"Shalom aleichem"}]
is_active: 1
---
code: he
name: Hebrew
native_name: עברית
script: Hebrew
words_json: [{"word":"שלום","translation":"peace","transcription":"shalom","phrases":"שלום עליכם"}]
is_active: 1

[RITUALS]
ritual_key: morning-practice
name: Morning Practice
description: Start the day warmly, eat first, then make important decisions.
description_format: plain
is_active: 1
---
ritual_key: evening-rest
name: Evening Rest
description: End the day quietly and avoid needless agitation.
description_format: plain
is_active: 1

[REMINDERS]
title: Morning Word of the Day
content_text: 
text_format: plain
frequency_mode: cron
schedule_preset: daily
interval_minutes: 
schedule_time: 09:11
schedule_timezone: Europe/Kyiv
schedule_weekday: 
schedule_monthday: 
schedule_year_month: 
schedule_year_day: 
frequency_value: 11 9 * * *
command_trigger: /morningNote
language_code: he
word_mode: 1
reminder_type_key: custom
is_active: 1
---
title: Evening Practice Note
content_text: Rest, eat, and do not decide while exhausted.
text_format: plain
frequency_mode: cron
schedule_preset: daily
interval_minutes: 
schedule_time: 21:00
schedule_timezone: Europe/Kyiv
schedule_weekday: 
schedule_monthday: 
schedule_year_month: 
schedule_year_day: 
frequency_value: 0 21 * * *
command_trigger: /eveningNote
language_code: en
word_mode: 0
reminder_type_key: custom
is_active: 1

[WORDS]
language_code: en
word: shalom
translation: peace
transcription: sha-lom
phrases: Shalom aleichem
is_active: 1
---
language_code: en
word: simcha
translation: joy
transcription: sim-kha
phrases: Serve with simcha
is_active: 1
---
language_code: he
word: שלום
translation: peace
transcription: shalom
phrases: שלום עליכם
is_active: 1
---
language_code: he
word: אמונה
translation: faith
transcription: emunah
phrases: חזק באמונה
is_active: 1
```

## Notes

- Empty values are allowed for optional fields.
- Multiple entries inside one section are separated with `---`.
- `word_mode: 1` makes the reminder use a random active word from `language_words`.
- `schedule_timezone` defaults to `Europe/Kyiv` if omitted, but it is better to provide it explicitly.
- `WORDS` is preferred over legacy `words_json`, but both are supported.

## Recommended Minimal Import

If you only want a working word-of-day bundle, keep:
- one religion
- one confession
- one language
- one reminder with `word_mode: 1`
- at least one word in `[WORDS]`
