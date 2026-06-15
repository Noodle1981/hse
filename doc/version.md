# XamaSafe Version Info

**Current Version:** 1.1.0
**Latest Release Date:** June 15, 2026

## Version History

### v1.1.0
- **Responsive Tables to Cards Transition:** Grid views automatically transition to mobile/tablet cards under 1024px breakpoint (`lg`).
- **Brand Scrollbars:** Added custom-themed orange, indigo, and red scrollbars to denote horizontal overflow capability clearly.
- **Dynamic Sticky Columns:** Upgraded annual planning matrix sticky columns to responsive widths (`w-[140px] sm:w-[220px] md:w-[300px]`).
- **Inertia Auth Safety:** Secured page routing logic by destructuring `auth` from `usePage().props` safely.
- **Role Separation:** Introduced Súper Administrador vs Vendedor role structures with restricted permissions and immutable audit logging.
- **Base64 Signatures Optimization:** Reduced database bloat by storing signatures physically in the public folder and saving only relative paths.
- **Asset Cleanup:** Removed legacy screenshot assets (`cap1.png`, `cap2.png`, `cap3.png`).

---
*XamaSafe Development Team © 2026*
