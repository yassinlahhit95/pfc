# 🚀 AULAPRO - INSTALACIÓN RÁPIDA

**Single Source of Truth:** `database.sql`

---

## ⚡ INSTALACIÓN EN 3 PASOS

### 1️⃣ Crear Base de Datos

**Opción A - phpMyAdmin:**
```
1. Abre: http://localhost/phpmyadmin
2. Click: "Nueva" o "Create"
3. Nombre: yassjjzw_pfc
4. Collation: utf8mb4_general_ci
5. Click: "Crear"
```

**Opción B - Terminal:**
```bash
mysql -h localhost -u root
CREATE DATABASE yassjjzw_pfc CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
EXIT;
```

---

### 2️⃣ Importar database.sql

**Opción A - phpMyAdmin:**
```
1. Selecciona BD: yassjjzw_pfc
2. Click: "Importar"
3. Selecciona: database.sql
4. Click: "Continuar"
✅ LISTO!
```

**Opción B - Terminal:**
```bash
mysql -h localhost -u root yassjjzw_pfc < database.sql
✅ LISTO!
```

**Opción C - Terminal con Usuario:**
```bash
mysql -h localhost -u yassjjzw_adminpfc -p yassjjzw_pfc < database.sql
# Ingresa password cuando se pida
✅ LISTO!
```

---

### 3️⃣ Copiar .env

```bash
cp .env.example .env
```

Ya tiene credenciales configuradas ✅

---

## ✅ VERIFICAR INSTALACIÓN

**En phpMyAdmin:**
```
1. Selecciona BD: yassjjzw_pfc
2. Debería ver 35+ tablas:
   ✅ niveles
   ✅ ciclos
   ✅ profesores
   ✅ estudiantes
   ✅ modulos
   ✅ aula_sesiones_vivas
   ✅ aula_tareas
   ✅ aula_entregas (con comentarioCalificacion + archivoCorreccion)
   ✅ ... y más
```

---

## 🎓 DATOS DE PRUEBA (Incluidos)

**Admin:**
```
Email: admin@aulapro.com
Pass:  123456
```

**Profesor:**
```
Email: juan.garcia@aulpro.com
Pass:  123456
```

**Estudiante:**
```
Email: carlos.sanchez@aulpro.com
Pass:  123456
```

---

## 📊 LO QUE INCLUYE database.sql

```
✅ 35+ Tablas completas
✅ Aula Digital: Sesiones Vivas
✅ Aula Digital: Tareas y Entregas
✅ Sistema de Seguridad
✅ Logging y Auditoría
✅ Datos de prueba (ciclos, profesores, estudiantes)
✅ Todas las columnas necesarias
✅ Todos los indexes de performance
✅ Todas las foreign keys
```

---

## 🚀 LISTO!

Después de importar `database.sql`, el sistema está **100% funcional**:

```
🎯 Seguridad              ✅ Activa
🎥 Sesiones Vivas        ✅ Lista
📝 Tareas y Entregas     ✅ Lista
🧪 Tests (37)            ✅ Listos
📊 Logging               ✅ Activo
```

**Accede a:** http://localhost/xampp/htdocs/pfc

¡Disfruta! 🎓
