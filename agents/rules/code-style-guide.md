---
trigger: always_on
---

1. PHP STANDARDS (PSR-2)
- **Database:** Always create a **Model** for any table update.
- **Validation:** Always create and use a **FormRequest** class.
- **Enum:** Use **PHP Enums** for status, type, and category columns.
- **Imports:** Always use `use` statements at the top. DO NOT use Fully Qualified Class Names (e.g., `\App\Models\User`) inline.
- **Safety:** Always check `method_exists()` or `function_exists()` before dynamic calls.

2. DATABASE & MIGRATIONS
- **Efficiency:** Only refresh/run specific modified migration files using `--path`. DO NOT run `migrate:refresh` or `migrate:fresh` on the entire folder.

3. COMMIT MESSAGE FORMAT
- Must be in **ENGLISH**.
- Use Conventional Commits style: `type(scope): description`.
## 5. MINIMAL DIFF & SCOPE CONTROL (CRITICAL)
- **No Unnecessary Cleanup:** DO NOT refactor or reformat existing code that is outside the scope of the current task.
- **Scope Locking:** Only modify lines that are directly related to the requested feature or bug fix.
- **Avoid Global Reformat:** Do not run automatic formatters (like phpcbf) on the entire file if it creates massive changes in the PR.
- **Preserve Legacy Style:** If the existing file doesn't follow PSR-2 perfectly, only apply PSR-2 to the NEW code you write. Do not "fix" the old code unless explicitly asked.
- **Preview Safety:** Prioritize "Clean Diffs" over "Perfect Codebase". If a change is not functional, omit it to keep the PR easy to review.
4. **CODE STYLE:**
   - Use English for variable names/comments.
   - Prefer functional patterns.
   - No "TODO" comments; implement full logic.
   - Use camelCase for variable names.
   - Use camelCase for method names.
   - Use snake_case for function names.
   - Use snake_case for table names.
   - Use snake_case for column names.
   - All PHP code MUST follow the Laravel style coding standard strictly.
5. Use the `HasMedia` trait for image fields (e.g., `thumbnail`, `banner`).
6. All main tables and their *_translations tables must have a `website_id` column for multi-website support.
7. Models using the `Translatable` trait must have a corresponding `ModelTranslation` class.
8. Use `scopeWhereFrontend` for theme and api queries. Use trait `Juzaweb\Modules\Admin\Traits\UsedInFrontend` to model if not exists.
9. Use the `home_url()` helper for theme and api URLs (not `url()`).
10. Theme code should follow the pattern in `themes/itech` (Public GitHub: https://github.com/juzaweb/itech).
11. The home page is always `index.blade.php` in the active theme folder.
12. When displaying an image (<img> tag) in a theme, use the proxy_image function to generate the appropriate src, srcset, and sizes.
13. To display banner ads in themes, use the [ads_position('position_key')](modules/core/helpers/themes.php:167:0-170:1) helper function instead of the Ads facade.
14. **Laravel Mix Build Commands:**
    - Build all assets (default to Admin module): `npm run prod`
    - Build specific module: `npm run prod --module=ModuleName` (e.g., `npm run prod --module=Admin`)
    - Build specific theme: `npm run prod --theme=ThemeName`
    - Run Mix Build when there are changes to related JavaScript and CSS.
15. ASSET MANAGEMENT (JS/CSS)
- **No Inline Styles/Scripts:** Strictly avoid writing CSS inside `<style>` tags or JS inside `<script>` tags within Blade files.
- **External Files First:** Priority must be given to creating or updating external `.js` and `.css` (or `.scss`) files.
- **Vite/Mix Integration:** When adding new assets, ensure they are properly imported/processed via Laravel Mix.
- **Blade Cleanliness:** Blade files should only contain HTML structure and essential Blade directives.
16. SERVICE PATTERN (BUSINESS LOGIC LAYER)
- **Inheritance:** All new Service classes MUST extend `Juzaweb\Modules\Core\Services\BaseService`.
- **Database Safety:** Use `$this->transaction(fn() => ...)` for operations involving multiple database changes. This leverages Laravel's native `DB::transaction()` for safety and nesting support.
- **Return Consistency:** Service methods should return a consistent structure (e.g., using `$this->result($status, $data, $message)`).
- **Naming:** Service files must end with the suffix `Service.php`.
17. **CSRF TOKEN VERIFICATION (TESTING)**
- **Disable for Testing:** Set `VERIFY_TOKEN=false` in [.env](.env) to disable CSRF token verification
