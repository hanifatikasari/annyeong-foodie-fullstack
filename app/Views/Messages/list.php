<?php if (! empty($messages)) : ?>
	<?php 
        // Kita kumpulkan semua pesan jadi satu teks untuk dicek warnanya
        $all_msg = implode(' ', $messages);
        
        // Logika IF ELSE untuk menentukan warna:
        // Jika ada kata 'invalid', 'incorrect', atau 'required', pakai MERAH (danger)
        // Jika tidak, pakai BIRU (info)
        $class = (preg_match('/invalid|incorrect|required|error|failed/i', $all_msg)) ? 'alert-danger' : 'alert-info';
    ?>
	
	<div class="alert alert-info" role="alert">
		<ul>
		<?php foreach ($messages as $message) : ?>
			<li><?= esc($message) ?></li>
		<?php endforeach ?>
		</ul>
	</div>
<?php endif ?>
