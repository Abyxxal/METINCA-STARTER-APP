# 🗄️ DBDiagram.io - Sintaks ERD METINCA

File ini berisi syntax yang **100% compatible** dengan dbdiagram.io

---

## **🚀 Cara Penggunaan:**

### **Langkah 1:**
Buka: https://dbdiagram.io/d

### **Langkah 2:**
Copy semua syntax dari file `DBDIAGRAM_METINCA.sql`

### **Langkah 3:**
Paste ke dalam editor dbdiagram.io

### **Langkah 4:**
Diagram akan otomatis muncul! ✨

---

## **📋 Sintaks DBDiagram.io Basics**

### **Buat Tabel**
```
Table users {
  id int [pk, increment]
  email varchar [unique]
  password varchar
  role varchar
}
```

### **Foreign Key Relationships**
```
Table posts {
  id int [pk, increment]
  user_id int [ref: > users.id]  // One to Many
  title varchar
}

Table comments {
  id int [pk, increment]
  post_id int [ref: > posts.id]
  user_id int [ref: > users.id]
}
```

### **Field Type Modifiers**

| Modifier | Keterangan |
|----------|-----------|
| `[pk]` | Primary Key |
| `[unique]` | Unique constraint |
| `[increment]` | Auto-increment |
| `[ref: > table.column]` | Foreign Key ke table lain |
| `[not null]` | Not nullable |
| `[default: value]` | Default value |

### **Relationship Symbols**

| Symbol | Arti |
|--------|------|
| `>` | One-to-Many (1:N) |
| `<` | Many-to-One (N:1) |
| `-` | One-to-One (1:1) |

---

## **13 Tabel dalam METINCA:**

### **ORGANIZATION LAYER** 🏢
- `departments` - Departemen organisasi
- `divisions` - Divisi dalam departemen
- `positions` - Posisi/jabatan
- `employees` - Data karyawan

### **AUTHENTICATION LAYER** 🔐
- `users` - User login
- `personal_access_tokens` - API tokens

### **COMPETENCY LAYER** 🎯
- `skills` - Daftar skill/kompetensi
- `employee_competencies` - Skill level per employee
- `division_skills` - Skill requirement per divisi

### **EXAMINATION LAYER** 📝
- `exams` - Ujian CBT
- `questions` - Soal/pertanyaan
- `exam_question` - Pivot table (soal dalam ujian)
- `exam_sessions` - Hasil ujian per employee
- `exam_answers` - Detail jawaban per soal

---

## **📊 Relationship Map**

```
departments
    ↓
divisions
    ├─→ employees ←─ users
    │       ↓
    │   exam_sessions
    │       ├─→ exam_answers
    │       ├─→ questions
    │       └─→ exams
    │
    ├─→ division_skills
    │       └─→ skills
    │
    └─→ employee_competencies
            ├─→ employees
            ├─→ skills
            └─→ users (verified_by)
```

---

## **💡 Tips DBDiagram.io**

### **1. Export Diagram**
- Download PNG
- Download PDF  
- Copy as image
- Get shareable link

### **2. Customize View**
- Dark/Light mode
- Zoom in/out
- Pan & organize layout
- Hide/show columns

### **3. SQL Export**
Klik "Export" → "Export SQL" untuk mendapatkan script migration

### **4. Collaboration**
Buat akun dan share link dengan tim

---

## **✅ Checklist**

- [x] Semua 13 tabel sudah didefinisikan
- [x] Semua foreign keys sudah di-set
- [x] Primary keys sudah di-mark
- [x] Relationships sudah benar
- [x] Timestamp fields di-include
- [x] Enum fields di-specify
- [x] Json fields di-include

---

## **🔗 Useful Links**

- **DBDiagram.io:** https://dbdiagram.io/
- **Documentation:** https://dbdiagram.io/docs
- **Example Projects:** https://dbdiagram.io/examples

---

**File:** `DBDIAGRAM_METINCA.sql`  
**Format:** DBDiagram.io SQL Syntax  
**Status:** ✅ Ready to Use  
**Last Updated:** May 12, 2026
