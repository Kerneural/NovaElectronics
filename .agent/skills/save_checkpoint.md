# Skill: Context Checkpointing & Handover Guide
> *Last updated: 2026-07-07 | NovaElectronics*

This skill helps compact the context budget of long conversations, saving tokens and preventing AI hallucination by creating structured handovers.

---

## 🛠️ Execution Protocol

When requested to checkpoint, perform these tasks:

### 1. Update `session_memory.md`
Compense findings into [session_memory.md](file:///r:/_Projects/Eurus_Workspace/dienmay_clone/.agent/workflows/session_memory.md) following this schema:
- **Active Tasks Completed:** Specific modified files and technical fixes.
- **Semantic Context Essence:** Crucial lessons learned and pitfall warnings (re-import image caches, naive regex template walking, Cp1258 encoding crashes, redirection hooks).
- **Next Steps:** Top 3 action items.

### 2. Update Plan
Mark completed items as `[x]` in [PLAN.md](file:///r:/_Projects/Eurus_Workspace/dienmay_clone/.agent/rules/PLAN.md).

### 3. Handover Prompt
Output a copy-pasteable text block for the developer to initiate the next session:

> **📋 [Handover Prompt]**
> *"Please run `/init` to read [session_memory.md](file:///r:/_Projects/Eurus_Workspace/dienmay_clone/.agent/workflows/session_memory.md), [CONTEXT.md](file:///r:/_Projects/Eurus_Workspace/dienmay_clone/.agent/rules/CONTEXT.md), and [PLAN.md](file:///r:/_Projects/Eurus_Workspace/dienmay_clone/.agent/rules/PLAN.md) to understand current progress and pitfall warnings, then proceed with the active item in Next Steps."*
