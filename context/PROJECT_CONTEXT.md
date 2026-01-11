# 📋 PROJECT CONTEXT - Warkop QR

## 🎯 Project Overview
**Nama:** Warkop QR - Sistem Pemesanan QR Code untuk Cafe  
**Tech Stack:** PHP Native + MySQL + Tailwind CSS  
**Status:** Frontend selesai (9 halaman HTML), Backend akan dikembangkan

---

## 📂 Struktur Project Saat Ini

```
warkop-qr/
├── app/
│   ├── controllers/     (kosong - untuk backend)
│   ├── core/           (App.php, Controller.php, Model.php)
│   ├── models/         (kosong - untuk backend)
│   └── views/          (kosong - untuk backend)
├── config/
│   └── database.php    (konfigurasi database)
├── context/            (dokumentasi project)
├── public/
│   └── index.php       (entry point)
└── routes/
    └── web.php         (routing)

HTML Files (root - frontend sementara):
├── landingphp        ✅ Customer: Welcome page + test tables
├── menuphp           ✅ Customer: Menu grid dengan cart
├── cartphp           ✅ Customer: Keranjang belanja
├── checkoutphp       ✅ Customer: Form checkout
├── order-successphp  ✅ Customer: Success page
├── admin-loginphp    ✅ Admin: Login page
├── admin-dashboardphp ✅ Admin: Dashboard dengan chart
├── admin-ordersphp   ✅ Admin: Kelola pesanan
├── admin-menusphp    ✅ Admin: CRUD menu
└── admin-reportsphp  ✅ Admin: Laporan & analytics
```

---

## 🎨 Design System

### Customer Pages (Mobile-First)
- **Layout:** Grid-based, responsive 2-5 columns
- **Style:** Modern cards dengan gradient headers
- **Colors:** Indigo-purple gradients untuk primary actions
- **Features:** Toast notifications, sticky headers, floating cart button

### Admin Pages (Desktop-First)  
- **Layout:** Fixed sidebar (w-64) + main content
- **Sidebar:** Dark theme (bg-gray-900), gradient active states
- **Charts:** Chart.js untuk analytics (revenue, orders, categories)
- **Colors:** Indigo-purple gradients, category-specific colors

### Color Palette
- Primary: `indigo-600` → `purple-600` (gradients)
- Kopi: `amber-500` → `orange-600`
- Non Kopi: `green-500` → `emerald-600`
- Makanan: `red-500` → `pink-600`
- Snack: `purple-500` → `indigo-600`
- Success: `green-500/600`
- Warning: `amber-500/600`

---

## 💾 Data Flow (LocalStorage - Temporary)

### Current Implementation
Semua data disimpan di **localStorage** browser (temporary, untuk testing):

```javascript
// Menus
localStorage.setItem('menus', JSON.stringify([...]));

// Cart
localStorage.setItem('cart', JSON.stringify([
  { id, name, category, price, quantity }
]));

// Orders
localStorage.setItem('orders', JSON.stringify([
  { orderNumber, tableNumber, customerName, items, total, status, timestamp }
]));

// Table Number
localStorage.setItem('tableNumber', '1');

// Auth (Admin)
localStorage.setItem('user', JSON.stringify({ 
  username, role, loginTime 
}));
```

---

## 🔄 System Flow

### Customer Journey
1. **Landing** → Scan QR / pilih meja test
2. **Menu** → Browse menu by category → Add to cart
3. **Cart** → Review items → Adjust quantity → Checkout
4. **Checkout** → Fill form (name, notes, payment method)
5. **Success** → Show order confirmation + timeline

### Admin Journey
1. **Login** → Authenticate (admin/kasir)
2. **Dashboard** → View stats, charts, recent orders
3. **Orders** → Manage orders (view, update status, delete)
4. **Menus** → CRUD menu items with categories
5. **Reports** → Analytics, top sellers, revenue by period

---

## 📊 Database Schema (To Be Implemented)

```sql
-- Tables untuk meja
tables (id, table_number, qr_token, status, created_at)

-- Menu items
menus (id, name, category, price, image, available, created_at)

-- Orders
orders (id, order_number, table_id, customer_name, notes, 
        payment_method, subtotal, tax, total, status, created_at)

-- Order items
order_items (id, order_id, menu_id, quantity, price, created_at)

-- Users (admin/kasir)
users (id, username, password, role, created_at)
```

---

## 🚀 Next Steps (Backend Development)

### Priority 1: Database & Models
- [ ] Setup MySQL database
- [ ] Create migration files
- [ ] Build Model classes (Menu, Order, Table, User)

### Priority 2: Authentication
- [ ] Implement login system (replace localStorage)
- [ ] Session management
- [ ] Role-based access control (admin/kasir)

### Priority 3: Core Features
- [ ] QR token validation
- [ ] Menu CRUD API
- [ ] Order processing system
- [ ] Status update workflow

### Priority 4: Integration
- [ ] Convert HTML pages to PHP views
- [ ] Connect forms to controllers
- [ ] Real-time order updates
- [ ] Payment gateway integration (optional)

---

## 🧪 Testing

### Current Test Mode
- Visit `landingphp` → Pilih meja 1-6
- Parameter URL: `?table=1`
- All data in localStorage
- No backend validation

### Demo Credentials (localStorage only)
- **Admin:** username=`admin`, password=`admin123`
- **Kasir:** username=`kasir`, password=`kasir123`

---

## 📝 Important Notes

1. **HTML files di root** = Frontend prototype untuk testing
2. **app/ folder** = Backend architecture (akan diisi kemudian)
3. **localStorage** = Temporary storage, akan diganti MySQL
4. **No auth yet** = Login hanya simulasi di frontend
5. **Mobile-first** = Customer pages optimized untuk HP
6. **Desktop-first** = Admin pages untuk layar besar

---

## 🎯 Key Features Completed

✅ Professional UI/UX untuk customer & admin  
✅ Responsive grid layouts  
✅ Chart.js integration untuk analytics  
✅ Toast notifications & modals  
✅ Category filtering & search  
✅ Cart management  
✅ Order flow simulation  
✅ Status timeline visualization  
✅ Payment method selection  
✅ Sticky headers & footers  

---

**Last Updated:** January 4, 2026  
**Status:** Frontend Complete, Ready for Backend Development
