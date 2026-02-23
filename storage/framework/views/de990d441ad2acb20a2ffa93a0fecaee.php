<h2>New Contact Message</h2>

<p><strong>Name:</strong> <?php echo e($contact->name); ?></p>
<p><strong>Email:</strong> <?php echo e($contact->email); ?></p>
<p><strong>Subject:</strong> <?php echo e($contact->subject); ?></p>

<hr>

<p><?php echo e($contact->message); ?></p>

<?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/emails/contact-notification.blade.php ENDPATH**/ ?>