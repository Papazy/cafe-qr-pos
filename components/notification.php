<?php
$successMessage = $_SESSION['success'] ?? null;
$errorMessage = $_SESSION['error'] ?? null;

unset($_SESSION['success']);
unset($_SESSION['error']);
?>

<?php if($successMessage): ?>
    <div class="toast fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 opacity-0 translate-y-[-10px] transition-all duration-500">
        <?= htmlspecialchars($successMessage) ?>
        <button onclick="this.parentElement.remove()" class="ml-3 font-bold">×</button>
    </div>
<?php endif; ?>

<?php if($errorMessage): ?>
    <div class="toast fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 opacity-0 translate-y-[-10px] transition-all duration-500">
        ❌ <?= htmlspecialchars($errorMessage) ?>
        <button onclick="this.parentElement.remove()" class="ml-3 font-bold">×</button>
    </div>
<?php endif; ?>


<script>
  document.querySelectorAll('.toast').forEach(toast => {
    // animate in
    setTimeout(() => {
      toast.classList.remove('opacity-0', 'translate-y-[-10px]')
      toast.classList.add('opacity-100', 'translate-y-0')
    }, 50)

    // animate out
    setTimeout(() => {
      toast.classList.add('opacity-0', 'translate-y-[-10px]')
    }, 3500)

    // remove element
    setTimeout(() => {
      toast.remove()
    }, 4000)
  })
</script>
