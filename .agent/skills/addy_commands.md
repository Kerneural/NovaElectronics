# Slash Commands Workflow Catalog
> *Last updated: 2026-07-07 | NovaElectronics*

This catalog defines quality gates for product development on NovaElectronics.

---

## 🛠️ Command Catalog

### 1. `/spec` (Specification Gate)
- **Goal:** Design spec definition before editing code.
- **Criteria:** Define target WooCommerce category slugs, physical image files, and rewrite paths.

### 2. `/plan` (Planning Gate)
- **Goal:** Update task roadmap.
- **Criteria:** Modify [PLAN.md](file:///r:/_Projects/Eurus_Workspace/dienmay_clone/.agent/rules/PLAN.md) with checklists.

### 3. `/build` (Implementation Gate)
- **Goal:** Code child theme.
- **Criteria:** Use dynamic queries. Never hardcode static URLs.

### 4. `/test` (Verification Gate)
- **Goal:** Test redirects and click counters.
- **Criteria:** Check database `_affiliate_clicks` metadata and verify HTTP 307 temporary redirects.

### 5. `/checkpoint` (Handover Gate)
- **Goal:** Compact memory.
- **Criteria:** Run seeder, run diff check, output handover prompt for session transition.
