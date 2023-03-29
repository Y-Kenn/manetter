<footer id="footer" class="p-footer">
    <span class="p-footer__text">Copyright MANETTER. All Rights Reserved.</span>
</footer>
<script>
    $(function(){
        var $footer = $('#footer');
        if(window.innerHeight > $footer.offset().top + $footer.outerHeight()){
            $footer.attr({'style' : 'position: fixed; top: ' + (window.innerHeight - $footer.outerHeight()) + 'px'});
        }
    });
</script>