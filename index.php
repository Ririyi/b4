<?php
// Считываем ошибки и предыдущие значения из cookies
$errors = json_decode($_COOKIE['errors'] ?? '{}', true);
$values = json_decode($_COOKIE['values'] ?? '{}', true);

// После использования удаляем cookie ошибок
setcookie('errors', '', time()-3600, "/");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Форма заявки</title>
<style>
  body { font-family: Arial, sans-serif; background: #f4f4f9; }
  .container { width: 600px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
  h2 { text-align: center; }
  label { display: block; margin-top: 10px; }
  input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
  textarea { height: 100px; }
  .radio-group, .checkbox-group { margin-top: 10px; }
  .error { color: red; font-size: 0.9em; }
  .input-error { border-color: red; }
  button { margin-top: 20px; padding: 10px; width: 100%; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
  button:hover { background: #218838; }
  .checkbox-group label { display: block; }
</style>
</head>
<body>
<div class="container">
<h2>Форма заявки</h2>
<form action="submit.php" method="POST">

  <label>ФИО:
    <input type="text" name="fio" required maxlength="150"
           class="<?=isset($errors['fio'])?'input-error':''?>"
           value="<?=htmlspecialchars($values['fio']??'')?>">
  </label>
  <?php if(isset($errors['fio'])) echo "<div class='error'>{$errors['fio']}</div>"; ?>

  <label>Телефон:
    <input type="tel" name="phone" required
           class="<?=isset($errors['phone'])?'input-error':''?>"
           value="<?=htmlspecialchars($values['phone']??'')?>">
  </label>
  <?php if(isset($errors['phone'])) echo "<div class='error'>{$errors['phone']}</div>"; ?>

  <label>E-mail:
    <input type="email" name="email" required
           class="<?=isset($errors['email'])?'input-error':''?>"
           value="<?=htmlspecialchars($values['email']??'')?>">
  </label>
  <?php if(isset($errors['email'])) echo "<div class='error'>{$errors['email']}</div>"; ?>

  <label>Дата рождения:
    <input type="date" name="birth_date" required
           class="<?=isset($errors['birth_date'])?'input-error':''?>"
           value="<?=htmlspecialchars($values['birth_date']??'')?>">
  </label>
  <?php if(isset($errors['birth_date'])) echo "<div class='error'>{$errors['birth_date']}</div>"; ?>

  <div class="radio-group">
    Пол: 
    <?php $g = $values['gender']??''; ?>
    <label><input type="radio" name="gender" value="male" <?=($g==='male')?'checked':''?>> Мужской</label>
    <label><input type="radio" name="gender" value="female" <?=($g==='female')?'checked':''?>> Женский</label>
  </div>
  <?php if(isset($errors['gender'])) echo "<div class='error'>{$errors['gender']}</div>"; ?>

  <label>Любимые языки программирования:</label>
  <div class="checkbox-group <?=isset($errors['languages'])?'input-error':''?>">
    <?php
    $langs = [
        1=>'Pascal',2=>'C',3=>'C++',4=>'JavaScript',5=>'PHP',
        6=>'Python',7=>'Java',8=>'Haskel',9=>'Clojure',10=>'Prolog',
        11=>'Scala',12=>'Go'
    ];

    // Приведение к массиву и к строкам
    $selected = (array)($values['languages'] ?? []);
    $selected = array_map('strval', $selected);

    foreach($langs as $id => $name){
        $checked = in_array((string)$id, $selected) ? 'checked' : '';
        echo "<label><input type='checkbox' name='languages[]' value='$id' $checked> $name</label>";
    }
    ?>
  </div>
  <?php if(isset($errors['languages'])) echo "<div class='error'>{$errors['languages']}</div>"; ?>

  <label>Биография:
    <textarea name="biography"><?=htmlspecialchars($values['biography']??'')?></textarea>
  </label>

  <div class="checkbox-group">
    <?php $agreed = $values['agreed']??0; ?>
    <label><input type="checkbox" name="agreed" value="1" <?=($agreed)?'checked':''?> required> С контрактом ознакомлен(а)</label>
  </div>
  <?php if(isset($errors['agreed'])) echo "<div class='error'>{$errors['agreed']}</div>"; ?>

  <button type="submit">Сохранить</button>
</form>
</div>
</body>
</html>
