# 🔐 Credenciales de Testing - EP-Back

## Sistema Actual (2 Roles)

### 👤 ADMIN / GESTOR
**Rol en BD:** `Administrador`  
**Email:** `admin@ep.com`  
**Password:** `password`  
**Permisos:** Gestión completa del sistema (empresas, sedes, usuarios, checklists, capacitaciones, etc.)

---

### 👤 OPERARI

---

## 📝 Notas Importantes

### Estructura de Roles Actual
El sistema actualmente tiene **2 roles**:
- **Administrador** = Gestor de empresa (admin@ep.com)
- **Técnico** = Operario de campo (operario@ep.com)

### Estructura Futura (Multi-Tenant)
Cuando se implemente multi-tenancy, habrá **3 roles**:
- **SuperAdmin** = Gestión de la plataforma completa
- **Administrador** = Gestor de empresa (actual)
- **Técnico** = Operario de campo (actual)

---

## 🧪 Uso para Testing

1. **Login como Admin** para probar:
   - Creación de empresas y sedes
   - Gestión de usuarios
   - Creación de checklists
   - Asignación de tareas
   - Reportes y dashboards

2. **Login como Operario** para probar:
   - Visualización de checklists asignados
   - Completado de inspecciones
   - Registro de hallazgos
   - Entregas de EPP
