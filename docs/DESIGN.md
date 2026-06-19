# Design System & UI/UX Guidelines - Aviona Sync

## 1. Design Direction

Aviona Sync must use a modern iOS-inspired visual style: clean, minimal, soft, professional, and highly readable. The interface should feel like a lightweight academic productivity app, not a complicated enterprise dashboard.

**Core vibe:** calm, focused, clean, fast, and reliable.

---

## 2. Language Policy

This document is written in English, but the final application UI must use Indonesian.

Examples of required UI copy:

```txt
Dashboard
Tambah Jadwal
Jadwal Terdekat
Tugas Mendekati Tenggat
Belum ada jadwal untuk minggu ini.
Simpan Perubahan
Tandai Selesai
```

Do not use English labels in the final UI.

---

## 3. Visual Principles

1. Use generous whitespace.
2. Use soft cards with large rounded corners.
3. Avoid harsh borders.
4. Use subtle shadows instead of heavy outlines.
5. Keep the interface calm and distraction-free.
6. Make urgent information visually noticeable but not aggressive.
7. Prioritize mobile-first readability.
8. Use consistent spacing, typography, and component patterns.
9. Use cards and badges to make academic deadlines easy to scan.

---

## 4. Typography

Use system sans-serif fonts.

Recommended Tailwind font stack:

```css
font-sans
```

Recommended hierarchy:

| Element | Tailwind Classes |
|---|---|
| Page title | `text-2xl md:text-3xl font-bold tracking-tight text-slate-900` |
| Section title | `text-lg font-semibold text-slate-900` |
| Card title | `text-base font-semibold text-slate-900` |
| Body text | `text-sm text-slate-600 leading-relaxed` |
| Muted text | `text-xs text-slate-500` |
| Badge text | `text-xs font-semibold` |

---

## 5. Color Palette

### 5.1 Base Colors

| Purpose | Tailwind Class |
|---|---|
| Main app background | `bg-slate-50` |
| Card background | `bg-white` |
| Main heading text | `text-slate-900` |
| Body text | `text-slate-600` |
| Muted text | `text-slate-500` |
| Soft divider | `border-slate-100` |

### 5.2 Primary Blue

Use blue for primary actions, active navigation, and important interactive elements.

| Purpose | Tailwind Class |
|---|---|
| Primary button | `bg-blue-600` |
| Primary hover | `hover:bg-blue-700` |
| Primary text | `text-blue-600` |
| Primary soft background | `bg-blue-50` |
| Primary ring | `focus:ring-blue-500` |

### 5.3 Yellow Accent

Use yellow only for urgency, reminders, and deadlines close to the due date.

| Purpose | Tailwind Class |
|---|---|
| Urgent badge background | `bg-yellow-100` |
| Urgent badge text | `text-yellow-800` |
| Warning highlight | `bg-yellow-400` or `bg-yellow-500` |
| Soft warning card | `bg-yellow-50` |

### 5.4 Status Colors

| Status | Background | Text |
|---|---|---|
| Pending | `bg-blue-50` | `text-blue-700` |
| Completed | `bg-emerald-50` | `text-emerald-700` |
| Cancelled | `bg-slate-100` | `text-slate-600` |
| Overdue | `bg-red-50` | `text-red-700` |
| Urgent | `bg-yellow-100` | `text-yellow-800` |

---

## 6. Spacing and Radius

Recommended spacing scale:

| Use Case | Tailwind Classes |
|---|---|
| Page wrapper | `px-4 sm:px-6 lg:px-8 py-6 md:py-8` |
| Section gap | `space-y-6` |
| Card padding | `p-5 md:p-6` |
| Form gap | `space-y-5` |
| Button padding | `px-4 py-2.5` |

Recommended radius:

| Component | Tailwind Classes |
|---|---|
| Main card | `rounded-3xl` |
| Small card | `rounded-2xl` |
| Input | `rounded-2xl` |
| Button | `rounded-2xl` |
| Badge | `rounded-full` |

---

## 7. Component Rules

### 7.1 Card

Every schedule, form, statistic, and dashboard section should be wrapped in a soft iOS-style card.

```html
<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6">
  <!-- Content -->
</div>
```

Card with subtle border:

```html
<div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6">
  <!-- Content -->
</div>
```

### 7.2 Primary Button

```html
<button class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100">
  Tambah Jadwal
</button>
```

### 7.3 Secondary Button

```html
<button class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 border border-slate-100 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">
  Batal
</button>
```

### 7.4 Input

```html
<input class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100" />
```

### 7.5 Select

```html
<select class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
  <!-- Options -->
</select>
```

### 7.6 Badge

```html
<span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">
  Mendesak
</span>
```

### 7.7 Empty State

```html
<div class="rounded-3xl bg-white p-8 text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
  <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
    <!-- Icon -->
  </div>
  <h3 class="text-base font-semibold text-slate-900">Belum ada jadwal</h3>
  <p class="mt-2 text-sm text-slate-500">Tambahkan jadwal pertama agar aktivitasmu lebih teratur.</p>
</div>
```

---

## 8. Page Layouts

### 8.1 Auth Pages

Auth pages should be centered and minimal.

```txt
Main background: bg-slate-50
Auth card: bg-white rounded-3xl shadow soft
Logo: centered
Form: vertical stack
Primary action: full width blue button
```

Required Indonesian labels:

```txt
Masuk
Daftar Akun
Nama Lengkap
Alamat Email
Kata Sandi
Konfirmasi Kata Sandi
Belum punya akun?
Sudah punya akun?
```

---

### 8.2 Dashboard Page

Recommended structure:

```txt
Header
├── Greeting
├── Date summary
└── Add schedule button

Stats Grid
├── Total Jadwal
├── Menunggu
├── Selesai
└── Mendesak

Main Content
├── Nearest schedule card
├── Upcoming schedule list
└── Urgent deadline list
```

Dashboard must highlight urgent activities without overwhelming the page.

---

### 8.3 Schedule Index Page

Recommended structure:

```txt
Header
├── Page title
└── Add schedule button

Filter Card
├── Search input
├── Category select
├── Status select
├── Priority select
└── Date sorting

Schedule List
├── Schedule card item
├── Status badge
├── Countdown
└── Quick actions
```

---

### 8.4 Form Page

Recommended structure:

```txt
Back link
Form card
├── Title input
├── Category select
├── Date/time input
├── Priority select
├── Location/link input
├── Description textarea
└── Submit/cancel actions
```

Forms must be easy to complete on mobile.

---

## 9. Schedule Card Design

Each schedule card must display:

1. Title.
2. Category badge.
3. Status badge.
4. Priority badge.
5. Date and time.
6. Location or link when available.
7. Countdown text.
8. Quick action buttons.

Example content hierarchy:

```txt
[Tugas] [Mendesak]
Pengumpulan Laporan K3
Jumat, 19 Juni 2026 • 23.59 WIB
Tenggat dalam 1 hari
[Detail] [Tandai Selesai]
```

---

## 10. Responsive Rules

1. Mobile-first is mandatory.
2. Use single-column layout on mobile.
3. Use two-column or three-column layout on desktop where appropriate.
4. Keep buttons large enough for touch interaction.
5. Avoid dense tables on mobile; use card lists instead.
6. Forms should be full width on mobile and limited width on desktop.

Recommended responsive classes:

```txt
grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4
flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between
max-w-5xl mx-auto
```

---

## 11. Microcopy Guidelines

The tone must be helpful, warm, and student-friendly.

Good:

```txt
Jadwal berhasil ditambahkan.
Tenggat waktu tinggal 1 hari lagi.
Belum ada tugas mendesak hari ini.
Periksa kembali data jadwalmu sebelum menyimpan.
```

Avoid:

```txt
Operation completed.
Deadline is approaching.
No urgent task.
```

---

## 12. Interaction Rules

1. Use Alpine.js only for lightweight interactions like dropdowns, mobile menus, modals, and confirmation dialogs.
2. Avoid unnecessary JavaScript complexity.
3. Add loading states for form submissions when possible.
4. Use confirmation dialogs for destructive actions.
5. Keep feedback messages visible and understandable.

---

## 13. Accessibility Rules

1. All inputs must have labels.
2. Buttons must have clear text or accessible labels.
3. Focus states must be visible.
4. Text contrast must be readable.
5. Do not rely only on color to communicate status.
6. Use semantic HTML where possible.

---

## 14. Design Quality Checklist

Before marking any UI complete, verify:

- The page uses Indonesian copy.
- Cards use `rounded-3xl` and soft shadow.
- Primary actions use blue.
- Urgent states use yellow.
- Text hierarchy is clear.
- Mobile layout is clean.
- Spacing is consistent.
- No harsh border-heavy dashboard style is used.
- Empty states are friendly.
- Form validation is readable.
