
<form enctype="multipart/form-data" method="POST" action="send.php">
    <label>Your Name <input type="text" name="sender_name" /> </label>
    <label>Your Email <input type="email" name="sender_email" /> </label>
    <label>Subject <input type="text" name="subject" /> </label>
    <label>Message <textarea name="message"></textarea> </label>
    <label>Attachment <input type="file" name="attachment" /></label>
    <label><input type="submit" name="button" value="Submit" /></label>
</form>
