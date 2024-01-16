<?php
/** @var app\models\Student $model */
/** @var string $forAction */
/** @var View $this */

use app\core\Application;
use app\core\form\TextareaField;
use app\core\View;
use \app\core\form\SelectionBoxField;

$this->title = 'Confirm Student';
?>
<h3>Confirm Student</h3>
<?php $form = \app\core\form\Form::begin('/confirmStudent', "post", 'subjectForm') ?>
<?php echo $form->field($model, 'name')->readOnlyField() ?>
<?php echo $form->field($model, 'avatar')->hiddenField() ?>
<input type="hidden" name="edit" value="">
<div>
    <img src="../web/avatar/<?php echo $model->avatar?>" class="img-fluid" alt="Avatar Image" style="width: 300px; height: auto">
</div>
<?php echo (new TextareaField($model, 'description'))->readOnlyField() ?>

<div class="mt-3">
    <div id="edit" class="btn btn-primary">Sửa lại</div>
    <button type="submit" class="btn btn-success">Đăng ký</button>
</div>
<?php \app\core\form\Form::end() ?>

<script>
    $('#edit').click(function() {
        // Thay đổi action của form
        $('#subjectForm').attr('action', '/registerStudent');
        $('input[name="edit"]').val('true');
        $('#subjectForm').submit();
    });
</script>
