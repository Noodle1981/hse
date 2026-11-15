# 09 — Control de Versiones (Git & GitHub)

- [x] **Repositorio remoto propio desacoplado**: `origin` apuntando exclusivamente a `https://github.com/Noodle1981/hse.git` sin referencias al repositorio anterior.
- [x] **Convención de commits (> 90%)**: Mensajes de commit con prefijo convencional (`feat:`, `fix:`, `docs:`, `chore:`, `refactor:`, `test:`).
- [x] **Etiquetado de versiones (Tags SemVer)**: Tag `v2.3.0` creado y publicado en `origin`.
- [x] **Configuración de exclusiones (`.gitignore`)**: Excluye `/database/*.sqlite`, `/public/hot`, `/public/build`, `/node_modules`, `/vendor`, `/storage/logs/*.log`, `/bootstrap/cache/*.tmp`.
- [x] **Privacidad de herramientas de auditoría (`.git/info/exclude`)**: Reglas locales configuradas para mantener `qa/plantillas/`, `qa/scripts/`, `qa/skills/`, `qa/alcance.md` y `qa/protocolo-pre-deploy.md` excluidos.
- [x] **Repositorio libre de binarios y volcados de base de datos**: `database/database copy.sqlite` eliminado y `git ls-files "database/*.sqlite"` vacío.
