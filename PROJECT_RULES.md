# Project Rules

## Purpose

These rules are mandatory for all future work in this repository. Review the existing implementation first, then extend it without drifting from the current backend structure or response contract.

## Architecture

- All backend feature work must follow `Controller -> Service -> Repository`.
- Controllers are orchestration only. Do not place business rules, persistence logic, or response-shaping logic directly in controllers.
- Validation must live in dedicated Form Request classes for endpoints that accept input.
- Entity payloads must be formatted through API Resource classes.
- All API responses must use `App\Helpers\ApiResponse`.

## API Contract

- Preserve the existing response shape exactly: `code`, `message`, `data`, and optional `pagination`.
- Do not return ad-hoc JSON structures directly from controllers when the project expects `ApiResponse::sendResponse(...)`.
- Keep pagination metadata structure consistent with the current project whenever paginated endpoints are added or updated.
- Reuse existing localization and translation patterns for response messages and model fields where applicable.

## Image And Media Handling

- All image upload, replacement, and deletion operations must use `App\Utils\ImageManger`.
- Use `uploadImage()` for single-image uploads.
- Use `uploadMultiImage()` when multiple images are stored.
- Use `deleteImage()` before replacing or removing stored media.
- Do not introduce a new image abstraction or scattered filesystem logic for features that already belong in `ImageManger`.

## Code Organization

- Keep classes in the namespaces and folders that match the current project structure.
- Do not bypass Services and Repositories from controllers.
- Use meaningful method names that describe the business action being performed.
- Preserve translatable fields and bilingual content support where models already use translated attributes.
- Before adding a new abstraction, inspect adjacent modules and mirror the existing project style unless there is a clear defect to fix.

## Seeder Rules

- Seeders must be idempotent and safe to run multiple times.
- Prefer `updateOrCreate()`, `firstOrCreate()`, or guarded `firstOrNew()` plus `fill()` and `save()` patterns.
- Seeder data must match the current migrations, model fillable fields, casts, and translation behavior.
- Default demo content must match the supermarket and grocery domain.
- Do not leave legacy data from prior projects, including FIX, mobile, spare-parts, or unrelated ecommerce modules.
- When seeders manage singleton content such as settings, about, privacy, or terms, they must update the existing record instead of creating duplicates.

## Modification Discipline

- Inspect current models, migrations, controllers, services, repositories, requests, resources, and helper utilities before implementing changes.
- Preserve existing conventions unless a change is required to fix a concrete bug or align the code with the documented architecture.
- Do not change the API response contract, introduce layer bypasses, or add unnecessary modules for features that are not part of the current project phase.
