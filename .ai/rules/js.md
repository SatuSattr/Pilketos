---
paths:
  - resources/js/app.js
---

# Js

## Toast notifications use Notyf, not SweetAlert2
Toast (transient notifications) use `notyf` (npm). The singleton is created at module scope in app.js with 4 custom types: success, error, warning, info — all referencing CSS variable tokens for color. `window.adminToast(type, message)` is the only public API. SweetAlert2 is kept exclusively for confirm dialogs (`window.adminConfirm`). Do not add a second toast system or call `Swal.mixin({ toast: true })` again.
