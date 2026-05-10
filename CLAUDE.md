# AulaPro – Project Rules

This project is made for a beginner-level PHP student.
Keep the code simple, readable, and easy to understand.
Do not rewrite the project architecture.
Do not make advanced improvements unless explicitly requested.

## Rules

- Use simple procedural PHP only
- Keep the current MVC structure exactly as it is
- Do not create new folders or files unless requested
- Do not move logic between Controllers and Models

## Controllers

- Validation must stay inside Controllers only
- Use simple validation methods only:
  - isset()
  - empty()
  - !empty()
  - is_numeric()
  - ctype_alpha()
  - regex only when necessary
  - trim() when reading data from forms
- Do not add advanced sanitization or filtering
- Do not use filter_input()
- Do not add unnecessary strtolower(), strtoupper(), ternary operations, unnecessary verification, null defaults, etc.
- Keep conditions simple and readable

## Models

- Models should contain only functions and SQL queries
- Do not add validation inside Models
- Do not create BaseModel or abstract layers
- Keep SQL queries simple and readable
- Avoid advanced joins, nested queries, optimizations, aliases, or complex logic unless necessary

## Code Style

- Avoid shortening the code in "smart" ways
- Simplicity is more important than reducing lines
- Do not over-optimize
- Prefer readable code over clever code
- Avoid unnecessary arrays, callbacks, helper functions, abstractions, or reusable systems

## Syntax

- Do not use alternative PHP syntax (endif, endforeach, endwhile, endfor)
- Use normal braces only: if () { } / foreach () { } / while () { }

## Variables

- Rename unclear or overly complex variable names into simple readable names
- Avoid very short or cryptic variable names
- Keep variable names beginner-friendly and understandable

## Important

- Do not delete HTML, CSS, JavaScript, or existing features
- Do not change the behavior or business logic
- Do not redesign the system
- Modify only the requested part
- Use the minimum amount of changes possible

The final code should look like it was written by a beginner/intermediate student, not by a senior developer or AI.
