# workflow_efficiency.md — NovaElectronics Workspace Best Practices
> *Last updated: 2026-07-07 | NovaElectronics*

This guide outlines rules to save token quota, optimize speed, and prevent repetition of bugs.

---

## 1. Token Budget Optimization
* **Context Budget:** Reading very large template files (like `index.php` or `footer.php` with thousands of lines) repeatedly wastes token budget. Use target bounds (`StartLine` and `EndLine`) in `view_file`.
* **Scratch scripts:** Instead of running complex commands that output hundreds of lines of directory listings or DB outputs directly into the terminal, write a python helper script to search/verify and output only a small summary or save to a file in the `scratch/` directory.

---

## 2. Docker & Database Command Efficiency
* **Docker Compose context:** When executing WP-CLI commands inside the containers, run `docker compose run --rm wp-cli wp <command>` to avoid spawning heavy container tasks that block system resources.
* **WP-CLI Execution:** Use `wp eval` or `wp eval-file` for complex PHP operations rather than modifying core WordPress files temporarily.

---

## 3. String Replacement Security
* **String Anchor Rule:** When editing child theme php templates, avoid naive character-walking regex scripts. Use explicit line number replacement (or unique search strings) to prevent wiping the rest of the template layout.
* **Backup verification:** Always run `git status` and `git diff` after editing templates to confirm only the intended lines changed and the rest of the HTML markup remains intact.
