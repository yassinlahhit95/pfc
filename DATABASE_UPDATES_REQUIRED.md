# ⚠️ DATABASE UPDATES REQUIRED

**Date:** 2026-05-29  
**Status:** MUST APPLY BEFORE PRODUCTION

---

## 📋 SUMMARY

The task and grading system requires 2 additional columns in the `aula_entregas` table that are not currently in the database schema.

**Impact:** ❌ **CRITICAL** - Grading functionality will NOT work without these columns

---

## 🔧 CHANGES NEEDED

### Table: `aula_entregas`

**Add these 2 columns:**

```sql
ALTER TABLE `aula_entregas`
ADD COLUMN IF NOT EXISTS `comentarioCalificacion` text DEFAULT NULL AFTER `nota`,
ADD COLUMN IF NOT EXISTS `archivoCorreccion` varchar(255) DEFAULT NULL AFTER `comentarioCalificacion`;
```

**Add these indexes for performance:**

```sql
ALTER TABLE `aula_entregas`
ADD INDEX IF NOT EXISTS `idx_tarea_estudiante` (`idTarea`, `idEstudiante`),
ADD INDEX IF NOT EXISTS `idx_estudiante_nota` (`idEstudiante`, `nota`);
```

---

## 📊 CURRENT vs UPDATED TABLE

### CURRENT Structure:
```
aula_entregas
├── idEntrega (PK)
├── idTarea (FK)
├── idEstudiante (FK)
├── archivoEntrega
├── respuesta
├── version
├── fechaEntrega
├── nota
├── estado
└── (missing) comentarioCalificacion ❌
└── (missing) archivoCorreccion ❌
```

### UPDATED Structure:
```
aula_entregas
├── idEntrega (PK)
├── idTarea (FK)
├── idEstudiante (FK)
├── archivoEntrega
├── respuesta
├── version
├── fechaEntrega
├── nota
├── estado
├── comentarioCalificacion ✅ (NEW)
├── archivoCorreccion ✅ (NEW)
└── Indexes ✅ (NEW)
```

---

## 🚀 HOW TO APPLY

### Option 1: phpMyAdmin (Easiest)

```
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Select database: yassjjzw_pfc (or your database name)
3. Click "SQL" tab
4. Paste this SQL:
```

```sql
ALTER TABLE `aula_entregas`
ADD COLUMN IF NOT EXISTS `comentarioCalificacion` text DEFAULT NULL AFTER `nota`,
ADD COLUMN IF NOT EXISTS `archivoCorreccion` varchar(255) DEFAULT NULL AFTER `comentarioCalificacion`;

ALTER TABLE `aula_entregas`
ADD INDEX IF NOT EXISTS `idx_tarea_estudiante` (`idTarea`, `idEstudiante`),
ADD INDEX IF NOT EXISTS `idx_estudiante_nota` (`idEstudiante`, `nota`);
```

```
5. Click "Go" button
6. Done! ✅
```

### Option 2: MySQL Command Line

```bash
mysql -h localhost -u yassjjzw_adminpfc -p yassjjzw_pfc < migration_aula_entregas.sql
# When prompted, enter password: Yassin1995**
```

### Option 3: Batch with database.sql

1. Update `database.sql` to include new columns in `aula_tareas` definition
2. Reimport entire database
3. Repopulate test data

---

## ✅ VERIFICATION

After applying the migration, verify the changes:

```sql
-- Check columns exist
DESCRIBE aula_entregas;

-- Should show:
-- comentarioCalificacion | text
-- archivoCorreccion      | varchar(255)

-- Check indexes
SHOW INDEX FROM aula_entregas;

-- Should show:
-- idx_tarea_estudiante
-- idx_estudiante_nota
```

---

## 📝 AFFECTED FEATURES

**Grading System Components:**

| Component | Status | Details |
|-----------|--------|---------|
| Student submits task | ✅ Works | File upload without grading |
| Professor sees submissions | ✅ Works | List of submissions |
| **Professor grades task** | ❌ BLOCKED | Needs `comentarioCalificacion` + `archivoCorreccion` |
| **Student sees grade** | ❌ BLOCKED | Needs feedback to display |
| **Student downloads feedback** | ❌ BLOCKED | Needs `archivoCorreccion` |

---

## 🔍 RELATED FILES

Files that depend on these columns:

```
✅ Depends on: comentarioCalificacion
├── vistas/profesores/aula/calificar.php (line 100+)
├── vistas/estudiantes/aula/tarea_detalle.php (line 120+)
├── vistas/admin/aula/entregas.php (line 130+)
└── controladores/profesores/aula/calificar_entrega.php (line 80+)

✅ Depends on: archivoCorreccion
├── vistas/profesores/aula/calificar.php (line 105+)
├── vistas/estudiantes/aula/tarea_detalle.php (line 125+)
└── controladores/profesores/aula/calificar_entrega.php (line 85+)
```

---

## ⚡ APPLY NOW!

**This is critical for the grading functionality.**

After applying the migration, the entire task and grading system will work perfectly:

```
👨‍🏫 Teacher grades task
  ↓
✅ Comments saved in comentarioCalificacion
✅ Correction file saved in archivoCorreccion
  ↓
👨‍🎓 Student receives grade
  ↓
✅ Sees feedback comment
✅ Can download correction file
```

---

## 📋 SCRIPT FILE

A ready-to-use SQL file is available:
```
migration_aula_entregas.sql
```

Use it to apply changes in one command!

---

## ❓ TROUBLESHOOTING

### Error: "Column already exists"
- This is OK! The `IF NOT EXISTS` clause prevents errors
- Your database already has the columns
- Verify with: `DESCRIBE aula_entregas;`

### Error: "Unknown table"
- Check database name is correct
- Default: `yassjjzw_pfc`
- Verify in phpMyAdmin

### MySQL connection refused
- Ensure XAMPP is running
- Check MySQL service is started
- Verify credentials in .env file

---

**Status:** REQUIRED ⏳ DO THIS BEFORE GRADING TASKS

*Generated: 2026-05-29*
