# CSMS Design System
**CLSU Green · Hostinger-Inspired · v1.0**

---

## 1. Design Philosophy

The CSMS UI follows a **clean, neutral-first** approach inspired by Hostinger's dashboard:

- **Page background is always gray** (`#F5F5F6`) — never white
- **Cards are always white** (`#ffffff`) with a subtle shadow
- **Brand green is used purposefully** — primary actions, active states, success indicators
- **No color noise** — avoid mixing blue, amber, violet, rose in the same view unless semantically required
- **Single accent per context** — LEC = green, LAB = blue, locked/error = rose, warning = amber
- **Typography drives hierarchy** — size and weight differences, not color differences

---

## 2. Color Tokens

### Brand (CLSU Green)

| Token | Hex | Usage |
|---|---|---|
| `brand.50` | `#f0fdf4` | Surface subtle, hover bg, active row bg |
| `brand.100` | `#dcfce7` | Tag bg, avatar bg, icon wrap bg |
| `brand.200` | `#bbf7d0` | Border emphasis, focus ring inner |
| `brand.600` | `#16a34a` | **PRIMARY** — buttons, icons, active indicators |
| `brand.700` | `#15803d` | Gradient start, text on tinted bg |
| `brand.800` | `#166534` | Badge text (active/success), dark text on green bg |

### Neutral (Slate)

| Token | Hex | Usage |
|---|---|---|
| `surface.default` | `#ffffff` | Card bg, modal bg, input bg |
| `surface.muted` | `#f8fafc` | Table head bg, footer bg, disabled input bg, striped row even |
| `surface.page` | `#F5F5F6` | Page/panel background (never use white for page bg) |
| `border.default` | `#e2e8f0` | All card borders, table borders, input borders |
| `border.emphasis` | `#bbf7d0` | Active/focused input border, brand-tinted borders |
| `text.primary` | `#0f172a` | Headings, bold labels, important values |
| `text.secondary` | `#475569` | Body text, descriptions, form values |
| `text.muted` | `#94a3b8` | Placeholder, helper text, timestamps, disabled |

### Semantic Colors

| Purpose | Bg | Text | Border/Ring |
|---|---|---|---|
| Success / Active | `#f0fdf4` | `#166534` | `#bbf7d0` |
| Warning / Pending | `#fffbeb` | `#92400e` | `#fcd34d` |
| Error / Danger | `#fff1f2` | `#9f1239` | `#fda4af` |
| Info / Neutral | `#f8fafc` | `#475569` | `#e2e8f0` |
| Lab / Blue | `#eff6ff` | `#1e40af` | `#bfdbfe` |
| Admin / Purple | `#faf5ff` | `#581c87` | `#d8b4fe` |
| Dean / Indigo | `#eef2ff` | `#3730a3` | `#a5b4fc` |
| Chair / Blue | `#eff6ff` | `#1e40af` | `#93c5fd` |

### Gradients

| Name | Value | Usage |
|---|---|---|
| `primary_cta` | `90deg, #003a10 0%, #009639 100%` | Primary button, page-header icon |
| `save_action` | `90deg, #009639 0%, #92d12c 100%` | Save button |
| `secondary_gold` | `90deg, #ffd700 0%, #e0a70d 100%` | Secondary/accent button |
| `hero_dark` | `135deg, #052e16 0%, #166534 60%, #15803d 100%` | Dark hero sections |

---

## 3. Typography

**Font family:** `'Plus Jakarta Sans', sans-serif`

| Role | Size | Weight | Line Height | Transform | Tracking | Color |
|---|---|---|---|---|---|---|
| Page title (h1) | `18px` (sm: `15px`) | `700` | `1.35` | — | — | `#0f172a` |
| Section heading (h2) | `15px` | `700` | `1.25` | — | — | `#0f172a` |
| Card title (h3) | `13px` | `700` | `1.35` | — | — | `#0f172a` |
| Overline / label | `11px` | `700` | `1` | `uppercase` | `0.12em` | `#475569` |
| Body / description | `13px` | `400` | `1.6` | — | — | `#475569` |
| Table cell | `13px` | `400–500` | `1.5` | — | — | `#0f172a` |
| Badge / pill | `11px` | `600` | `1` | — | — | varies |
| Helper / muted | `13px` | `400` | `1.5` | — | — | `#94a3b8` |
| Mono code | `13px` | `700` | `1` | — | — | `#166534` |

### Rules
- Never use `text-xs` (12px) for body content — minimum readable size is `text-[13px]`
- Overlines always: `text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]`
- Page header title: `text-[15px] sm:text-[18px] font-bold text-[#0f172a]`
- Modal header title: `text-[15px] font-bold text-[#0f172a]`
- Section header (inside card): `text-[13px] font-bold`

---

## 4. Spacing

Based on a 4px base unit:

| Token | Value | Tailwind | Usage |
|---|---|---|---|
| `space.1` | `4px` | `p-1` | Icon padding, tight gaps |
| `space.2` | `8px` | `p-2` | Button icon padding, small gaps |
| `space.3` | `12px` | `p-3` | Compact card padding, table cell padding |
| `space.4` | `16px` | `p-4` | Standard card body padding |
| `space.5` | `20px` | `p-5` | Card body padding (preferred) |
| `space.6` | `24px` | `p-6` | Modal body padding |
| `space.8` | `32px` | `p-8` | Large section padding |

### Component-specific spacing

| Component | Padding | Gap |
|---|---|---|
| Page header | `px-4 sm:px-6 py-4` | `gap-4` |
| Card header strip | `px-5 py-3` | `gap-2.5` |
| Card body | `p-5` | `space-y-4` or `space-y-5` |
| Table cell (`td`) | `px-4 py-3` | — |
| Table header (`th`) | `px-4 py-3` | — |
| Modal header | `px-6 py-4` | `gap-4` |
| Modal body | `p-6` | `space-y-4` |
| Modal footer | `px-6 py-4` | `gap-3` |
| Button (form) | `px-4 py-2.5` | `gap-2` |
| Button (table/sm) | `px-2.5 py-1.5` | `gap-1.5` |
| Status indicator/badge | `px-2.5 py-0.5` | `gap-1` |
| Form input | `px-3 py-2` | — |
| Form label → input | `mb-1` | — |
| Form field groups | `space-y-4` | — |
| Section between sections | `mb-5` or `mb-6` | — |

---

## 5. Border Radius

| Token | Value | Usage |
|---|---|---|
| `radius.sm` | `6px` | `rounded-md` — code badges, small chips |
| `radius.md` | `8px` | `rounded-lg` — buttons, inputs, selects, small cards |
| `radius.lg` | `12px` | `rounded-xl` — cards, modals, accordions, table containers |
| `radius.xl` | `16px` | `rounded-2xl` — large feature cards (avoid — prefer xl) |
| `radius.pill` | `999px` | `rounded-full` — status badges, avatars, dots |

### Rules
- **All cards, modals, accordions:** `rounded-xl`
- **All buttons:** `rounded-lg`
- **All inputs, selects, textareas:** `rounded-lg`
- **Status badges/pills:** `rounded-full`
- **Code/mono badges:** `rounded-md`
- Avoid `rounded-2xl` on new components — use `rounded-xl`

---

## 6. Shadows

| Token | Value | Usage |
|---|---|---|
| `shadow.card` | `0 2px 16px rgba(0,0,0,.07)` | All white cards, modals, table containers |
| `shadow.elevated` | `0 8px 32px rgba(22,163,74,.13)` | Modal dialogs (green-tinted) |
| `shadow.focus` | `0 0 0 3px rgba(22,163,74,.25)` | Input/select focus ring (via inline style) |

### Rules
- Always use `style="box-shadow: 0 2px 16px rgba(0,0,0,.07);"` on white cards (not Tailwind `shadow-sm`)
- Modal dialogs use `style="box-shadow: 0 8px 32px rgba(22,163,74,0.13);"` (elevated, green-tinted)
- Focus rings on inputs: applied via `onfocus`/`onblur` inline JS since Tailwind purges arbitrary ring values
- Never use `shadow-md`, `shadow-lg` — use the token values above

---

## 7. Borders

| Context | Class | Value |
|---|---|---|
| Default card/input border | `border border-[#e2e8f0]` | 1px solid `#e2e8f0` |
| Emphasis border (brand) | `border border-[#bbf7d0]` | 1px solid `#bbf7d0` |
| Danger border | `border border-[#fda4af]` | 1px solid `#fda4af` |
| Warning border | `border border-[#fcd34d]` | 1px solid `#fcd34d` |
| Dashed empty state | `border-2 border-dashed border-[#e2e8f0]` | 2px dashed |
| Table divider | `divide-y divide-[#e2e8f0]` | — |
| Page header bottom | `border-b border-[#e2e8f0]` | — |

---

## 8. Components

### Page Header (`x-page-header`)
```
bg: #ffffff
border-bottom: 1px solid #e2e8f0
shadow: shadow.card
padding: px-4 sm:px-6 py-4
icon: w-10 h-10 sm:w-11 sm:h-11, rounded-xl, gradient primary_cta, shadow 0 2px 8px rgba(22,163,74,0.2)
title: text-[15px] sm:text-[18px] font-bold text-[#0f172a]
desc: text-[13px] text-[#475569]
```

### Panel (`x-panel`)
```
bg: #F5F5F6  ← page background, NOT white
padding: px-2 sm:px-4 md:px-6 py-4 sm:py-6
```

### Card (`x-card`)
```
bg: #ffffff
border: 1px solid #e2e8f0
border-radius: rounded-xl
shadow: shadow.card
header strip padding: px-4 py-3
header title: text-[13px] font-bold
body padding: p-4
```

### Wizard Section (`x-wizard.section`)
```
Same as x-card but with colored header strip per color prop:
  brand/emerald: bg-[#f0fdf4] border-[#bbf7d0], icon bg-[#dcfce7] text-[#16a34a], title text-[#166534]
  blue (LAB):    bg-[#eff6ff] border-[#bfdbfe], icon bg-[#dbeafe] text-[#1d4ed8], title text-[#1e40af]
  slate:         bg-[#f8fafc] border-[#e2e8f0], icon bg-[#e2e8f0] text-[#475569], title text-[#0f172a]
```

### Step Header (`x-wizard.step-header`)
```
border-bottom: 1px solid #e2e8f0
margin-bottom: mb-6 pb-5
icon: w-9 h-9 rounded-xl, gradient primary_cta, shadow 0 2px 8px rgba(22,163,74,0.2)
title: text-[15px] font-bold text-[#0f172a]  ← on same row as action buttons
desc: text-[13px] text-[#475569], mt-2, pl-12 (when icon present)
action slot: shrink-0, same row as title (never wraps)
```

### Accordion (`x-accordion`)
```
bg: #ffffff
border: 1px solid #e2e8f0
border-radius: rounded-xl
shadow: shadow.card
header padding: px-5 py-4
header hover: bg-[#f8fafc]
title: text-[13px] font-bold text-[#0f172a]
chevron: text-[#94a3b8] text-lg
body border-top: 1px solid #e2e8f0
body padding: p-5
```

### Empty State (`x-empty-state`)
```
border: 2px dashed #e2e8f0
bg: #f8fafc
border-radius: rounded-xl
padding: p-8 sm:p-10
icon wrap: w-14 h-14 rounded-xl bg-[#f0fdf4] text-[#16a34a]
icon size: text-3xl
title: text-[15px] font-bold text-[#0f172a]
message: text-[13px] text-[#475569] max-w-sm mx-auto leading-relaxed
```

### Modal Dialog (`x-modal.dialog`)
```
border-radius: rounded-xl
bg: #ffffff
shadow: shadow.elevated (0 8px 32px rgba(22,163,74,0.13))
max-height: max-h-[88vh]
```

### Modal Header (`x-modal.header`)
```
padding: px-6 py-4
border-bottom: 1px solid #e2e8f0
bg: #ffffff
title: text-[15px] font-bold text-[#0f172a]
close button: p-1.5 rounded-lg text-[#94a3b8] hover:bg-[#f8fafc] hover:text-[#475569]
```

### Modal Body (`x-modal.body`)
```
padding: p-6
overflow-y: auto
flex: flex-1 min-h-0
```

### Modal Footer (`x-modal.footer`)
```
padding: px-6 py-4
border-top: 1px solid #e2e8f0
bg: #f8fafc
justify: justify-end (default)
gap: gap-3
```

### Modal Info Card (inside modal body)
```
border: 1px solid #e2e8f0
bg: #f8fafc
border-radius: rounded-xl
padding: p-4
row label: text-[11px] font-bold uppercase tracking-[0.12em] text-[#94a3b8]
row value: text-[13px] text-[#0f172a] or text-[#475569]
row layout: flex items-center justify-between
```

---

## 9. Table

```
Container:  rounded-xl border border-[#e2e8f0] bg-white, shadow.card
<thead>:    bg-[#f8fafc] border-b border-[#e2e8f0]
<th>:       px-4 py-3, text-[11px] font-bold uppercase tracking-[0.12em] text-[#475569]
<tbody>:    divide-y divide-[#e2e8f0]
<tr> hover: hover:bg-[#f0fdf4]
<tr> even:  bg-[#f8fafc]  (when striped)
<td>:       px-4 py-3, text-[13px] text-[#0f172a]
Empty row:  py-10 text-[13px] text-[#94a3b8] italic text-center
```

---

## 10. Buttons

| Variant | Background | Text | Border | Radius | Padding |
|---|---|---|---|---|---|
| `primary` | gradient `#003a10→#009639` | white | none | `rounded-lg` | `px-4 py-2.5` |
| `save` | gradient `#009639→#92d12c` | white | none | `rounded-lg` | `px-4 py-2.5` |
| `secondary` | gradient `#ffd700→#e0a70d` | `#1a5f30` | none | `rounded-lg` | `px-4 py-2.5` |
| `add-button` | `#16a34a` solid | white | none | `rounded-lg` | `px-4 py-2.5` |
| `cancel` / `back` | white | `#475569` | `1px #e2e8f0` | `rounded-lg` | `px-4 py-2.5` |
| `danger` | `#e11d48` solid | white | none | `rounded-lg` | `px-4 py-2.5` |
| `outline` | white | `#1a5f30` | `2px #1a5f30` | `rounded-lg` | `px-4 py-2.5` |
| `sm-add` | `#16a34a` solid | white | none | `rounded-lg` | `px-4 py-2` |
| `sm-cancel` | white | `#475569` | `1px #e2e8f0` | `rounded-lg` | `px-4 py-2` |
| `table-*` | varies | white | none | `rounded-lg` | `px-2.5 py-1.5` |

**Focus ring (all buttons):** `0 0 0 3px rgba(22,163,74,0.25)`

**Back buttons always use:** `<i class="bx bx-arrow-left"></i>` icon

---

## 11. Form Inputs

```
All inputs/selects/textareas:
  border:       1px solid #e2e8f0
  bg:           #ffffff
  border-radius: rounded-lg
  padding:      px-3 py-2
  font-size:    text-[13px]
  text-color:   #0f172a
  placeholder:  #94a3b8
  hover-border: #bbf7d0
  focus-border: #16a34a
  focus-ring:   box-shadow: 0 0 0 3px rgba(22,163,74,0.25)  ← via inline JS
  disabled-bg:  #f8fafc
  disabled-text: #94a3b8

Label:
  font-size:    text-[11px]
  font-weight:  font-bold (700)
  transform:    uppercase
  tracking:     tracking-[0.12em]
  color:        #475569
  margin-bottom: mb-1
  required star: text-rose-500
```

---

## 12. Status Indicators / Badges

```
All badges:
  font-size:    text-[11px]
  font-weight:  font-semibold (600)
  padding:      px-2.5 py-0.5
  border-radius: rounded-full
  gap:          gap-1

Dot size: w-1.5 h-1.5 rounded-full
Icon size: text-[11px]
```

---

## 13. Alerts (`x-feedback-status.alert`)

```
border-radius: rounded-xl
padding: p-4
icon wrap: w-7 h-7 rounded-lg (NOT rounded-full)
icon size: text-base
title: text-[13px] font-semibold
body: text-[13px] leading-relaxed

Types:
  success: border-[#bbf7d0] bg-[#f0fdf4] text-[#166534], icon bg-[#dcfce7] text-[#16a34a]
  warning: border-[#fcd34d] bg-[#fffbeb] text-[#92400e], icon bg-[#fef3c7] text-[#f59e0b]
  error:   border-[#fda4af] bg-[#fff1f2] text-[#9f1239], icon bg-[#ffe4e6] text-[#f43f5e]
  info:    border-[#e2e8f0] bg-[#f8fafc] text-[#0f172a], icon bg-[#e2e8f0] text-[#475569]
```

---

## 14. Sticky Navigation (Syllabus Wizard)

```
Right sidebar nav:
  width: w-60
  position: sticky; top: 5rem; align-self: flex-start
  max-height: calc(100vh - 6rem); overflow-y: auto
  border: 1px solid #e2e8f0
  bg: #ffffff
  shadow: shadow.card
  border-radius: rounded-xl

Nav header:
  padding: px-4 py-3
  bg: #F5F5F6
  border-bottom: 1px solid #e2e8f0
  label: text-[11px] font-bold uppercase tracking-[0.15em] text-[#797980]

Nav item (active):
  bg: #f0fdf4
  text: #15803d font-semibold
  border-left: 3px solid #16a34a
  padding: px-4 py-2.5

Nav item (inactive):
  text: #58585e
  hover-bg: #F5F5F6
  border-left: 3px solid transparent

Step number circle:
  active/done: bg-[#16a34a] text-white w-6 h-6 rounded-full
  inactive:    bg-slate-100 text-slate-400

Missing indicator dot: w-2 h-2 rounded-full bg-amber-400

Progress bar:
  track: bg-slate-100 h-1.5 rounded-full
  fill: gradient primary_cta
```

---

## 15. Color Usage Rules (Anti-patterns)

| ❌ Don't | ✅ Do |
|---|---|
| Use `bg-emerald-*` Tailwind classes | Use `bg-[#f0fdf4]`, `bg-[#dcfce7]` hex tokens |
| Use `bg-blue-*` for LAB | Use `bg-[#eff6ff]` / `text-[#1e40af]` |
| Use `bg-gray-*` in modals | Use `bg-[#f8fafc]` |
| Use `text-slate-*` for body | Use `text-[#475569]` or `text-[#0f172a]` |
| Use `shadow-sm` on cards | Use `style="box-shadow: 0 2px 16px rgba(0,0,0,.07);"` |
| Use `rounded-2xl` on new cards | Use `rounded-xl` |
| Use `text-xs` (12px) for body | Use `text-[13px]` minimum |
| Use `text-sm` (14px) for labels | Use `text-[11px] uppercase tracking-[0.12em]` for overlines |
| Mix amber/blue/violet in same view | Use single semantic color per context |
| Use `bx-arrow-back` for back buttons | Use `bx-arrow-left` |
| Nest cards inside cards | Use dividers (`border-t border-[#e2e8f0]`) instead |
| Use `overflow-hidden` on sticky parent | Keep panel/container without overflow-hidden |

---

## 16. LEC vs LAB Color Convention

| Component | LEC | LAB |
|---|---|---|
| Section header | `color="emerald"` → brand green | `color="blue"` → `#eff6ff/#bfdbfe` |
| Tab active | `text-[#166534] ring-[#bbf7d0]` | `text-[#1e40af] ring-[#bfdbfe]` |
| Schedule chip | `bg-[#f0fdf4] border-[#bbf7d0]` | `bg-[#eff6ff] border-[#bfdbfe]` |
| Dot indicator | `bg-[#16a34a]` | `bg-[#3b82f6]` |
| Status badge | `variant="brand"` | `variant="lab"` |

---

## 17. Locked / Special Week Colors

| State | Accent bar | Header bg | Circle | Text |
|---|---|---|---|---|
| Normal week | `bg-[#16a34a]` | `bg-[#f0fdf4]` | `bg-[#dcfce7] text-[#15803d]` | `text-[#0f172a]` |
| Locked (exam/non-teaching) | `bg-rose-500` | `bg-rose-50` | `bg-rose-100 text-rose-700` | `text-rose-700` |
| MVGO (Week 1) | `bg-[#16a34a]` | `bg-[#f0fdf4]` | same as normal | badge: `variant="slate"` |

---

## 18. File Reference

| What | Where |
|---|---|
| Design tokens (JSON) | `design.json` |
| Page background | `components/panel.blade.php` |
| Page header | `components/page-header.blade.php` |
| Card | `components/card.blade.php` |
| Accordion | `components/accordion.blade.php` |
| Empty state | `components/empty-state.blade.php` |
| Button | `components/button.blade.php` |
| Alert | `components/feedback-status/alert.blade.php` |
| Status badge | `components/feedback-status/status-indicator.blade.php` |
| Form inputs | `components/form/*.blade.php` |
| Modal components | `components/modal/*.blade.php` |
| Wizard section | `components/wizard/section.blade.php` |
| Wizard step header | `components/wizard/step-header.blade.php` |
