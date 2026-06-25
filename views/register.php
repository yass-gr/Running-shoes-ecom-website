<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
  </head>
  <body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <main class="auth-page">
      <div class="auth-form">
        <h1>Create Account</h1>

        <?php if (isset($error)): ?>
          <p class="auth-form__error"><?= e($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="?route=register">
          <div>
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required />
          </div>
          <div>
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required />
          </div>
          <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required />
          </div>
          <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />
          </div>
          <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="?route=login">Login here</a></p>
      </div>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
  </body>
</html>
