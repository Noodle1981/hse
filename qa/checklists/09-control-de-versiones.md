# 09 — Control de Versiones (Git & GitHub)

- [ ] **Repositorio remoto propio desacoplado**: `origin` apuntando exclusivamente a `https://github.com/Noodle1981/hse.git` sin referencias al repositorio anterior.
- [ ] **Convención de commits (> 90%)**: Mensajes de commit con prefijo convencional (`feat:`, `fix:`, `docs:`, `chore:`, `refactor:`, `test:`). *(Medición actual: **92%** en los últimos 50 commits; evitar commits genéricos como `"Update"` o `"mejoras varias"`).*
- [ ] **Etiquetado de versiones (Tags SemVer)**: Crear tags de versión (`v2.3.0`, etc.) en cada hito estable o pase a producción. *(Medición actual: **0 tags** en 258 commits).*
- [ ] **Configuración de exclusiones (`.gitignore`)**: Excluir `database/*.sqlite`, `public/hot`, `public/build`, `node_modules/`, `vendor/` y archivos temporales.
- [ ] **Privacidad de herramientas de auditoría (`.git/info/exclude`)**: Reglas locales configuradas mediante `setup-helper.js ignorar` para mantener `qa/plantillas/`, `qa/scripts/`, `qa/skills/`, `qa/alcance.md` y `qa/protocolo-pre-deploy.md` excluidos.
- [ ] **Repositorio libre de binarios y volcados de base de datos**: Eliminar `database/database copy.sqlite` del seguimiento de Git.
