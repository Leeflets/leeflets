<div class="hero-unit">
    <h1>Choose Wisely</h1>
    
    <p>Below is a list of leeflets that are currently installed and available for you to use. Click <strong>Activate</strong> to use a leeflet or <strong>Delete</strong> to delete a leeflet. If you don't see anything you like, you can purchase new leeflets below as well.</p>
    
    <p class="hero-nav">
        <a class="btn btn-info btn-large refresh">Install New Leeflets</a> 
        <a class="btn btn-success btn-large" href="#marketplace" data-toggle="tab">Purchase New Leeflets</a>
    </p>
</div>

<div class="page-header extra-margin">
    <h1>Currently Installed Leeflets <small>Choose the leeflet you would like to use for this site.</small></h1>
</div> 

<ul class="thumbnails">
    <?php get_available_leeflets(); // Get Installed Leeflets ?>
</ul>