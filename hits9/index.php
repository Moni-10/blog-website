
<?php 
require __DIR__ . '/includes/bootstrap.php'; 

$categories = get_categories(); 
$heroFeed = get_blog_feed(['limit' => 6]); 
$heroBlogs = $heroFeed['blogs']; 
$hero = $heroBlogs[0] ?? null; 
$heroRest = array_slice($heroBlogs, 1, 3); 

$pageTitle = site_tagline() ? site_name() . ' — ' . site_tagline() : site_name(); 

$pageDescription = 'Latest headlines and stories from ' . site_name() . ': ' . 
    implode(', ', array_map(
        fn($c) => $c['name'], 
        array_slice($categories, 0, 6)
    )) . ' and more.'; 

$canonicalUrl = 'https://' . SITE_DOMAIN . '/'; 
$ogImage = $hero ? featured_image($hero) : ''; 

require __DIR__ . '/includes/header.php'; 
?> 

<!-- banner start -->
<!--<section class="homepage-carousel" aria-roledescription="carousel" aria-label="Featured highlights" data-carousel>-->
<!--  <div class="homepage-carousel__viewport">-->
<!--    <div class="homepage-carousel__track">-->
<!--      <article class="homepage-carousel__slide is-active" aria-hidden="false">-->
<!--        <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=85" alt="Mountain lake surrounded by forest" width="1800" height="800">-->
<!--        <div class="homepage-carousel__content"><span>EXPLORE MORE</span>-->
<!--          <h2>Stories that take you places.</h2>-->
<!--          <p>Find fresh inspiration for your next great escape.</p><a class="homepage-carousel__button" href="/search/">Explore stories <span aria-hidden="true">→</span></a>-->
<!--        </div>-->
<!--      </article>-->
<!--      <article class="homepage-carousel__slide" aria-hidden="true">-->
<!--        <img src="https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1800&q=85" alt="Bright modern home interior" width="1800" height="800" loading="lazy">-->
<!--        <div class="homepage-carousel__content"><span>MAKE IT YOURS</span>-->
<!--          <h2>Ideas for everyday living.</h2>-->
<!--          <p>Simple discoveries, thoughtful spaces and better routines.</p><a class="homepage-carousel__button" href="/search/?q=lifestyle">Discover lifestyle <span aria-hidden="true">→</span></a>-->
<!--        </div>-->
<!--      </article>-->
<!--      <article class="homepage-carousel__slide" aria-hidden="true">-->
<!--        <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1800&q=85" alt="Notebook, pen and cup of coffee on a desk" width="1800" height="800" loading="lazy">-->
<!--        <div class="homepage-carousel__content"><span>THE WEEKLY EDIT</span>-->
<!--          <h2>Read something worth sharing.</h2>-->
<!--          <p>Practical ideas and new perspectives, selected for you.</p><a class="homepage-carousel__button" href="/search/">Read the latest <span aria-hidden="true">→</span></a>-->
<!--        </div>-->
<!--      </article>-->
<!--    </div>-->
<!--    <button class="homepage-carousel__arrow homepage-carousel__arrow--prev" type="button" aria-label="Previous banner" data-carousel-prev>‹</button>-->
<!--    <button class="homepage-carousel__arrow homepage-carousel__arrow--next" type="button" aria-label="Next banner" data-carousel-next>›</button>-->
<!--    <div class="homepage-carousel__dots" role="tablist" aria-label="Choose banner">-->
<!--      <button type="button" role="tab" aria-label="Show banner 1" aria-selected="true" data-carousel-dot></button>-->
<!--      <button type="button" role="tab" aria-label="Show banner 2" aria-selected="false" data-carousel-dot></button>-->
<!--      <button type="button" role="tab" aria-label="Show banner 3" aria-selected="false" data-carousel-dot></button>-->
<!--    </div>-->
<!--  </div>-->
<!--</section>-->
<!-- banner end -->

<?php if (!$heroFeed['ok']): ?> 

  <section class="wrap empty-state"> 
    <h1>We couldn't load the latest stories</h1> 
    <p>Please try again shortly.</p> 
  </section> 

<?php else: ?> 


  <?php if ($hero): ?> 




  <?php endif; ?> 



  <!-- =====================================================
       CATEGORY SECTIONS
       EACH CATEGORY:
       1 BIG CARD
       +
       3 SMALL CARDS
       ===================================================== -->

  <?php foreach ($categories as $cat): 

      /*
       * Get 4 posts:
       * 1st = BIG CARD
       * 2nd, 3rd, 4th = SMALL CARDS
       */

      $section = get_blog_feed([
          'category' => $cat['slug'],
          'limit' => 4
      ]);

      $categoryBlogs = $section['blogs'] ?? [];

      if (!$categoryBlogs) {
          continue;
      }

      $categoryHero = $categoryBlogs[0];

      $categoryRest = array_slice(
          $categoryBlogs,
          1,
          3
      );

  ?> 


  <!-- =========================
       CATEGORY
       ========================= -->

  <section class="wrap home-section"> 


    <!-- CATEGORY HEADING -->

    <div class="home-section-head">

      <h2>
        <?= e($cat['name']) ?>
      </h2>

      <a 
        class="view-all" 
        href="<?= e_attr(category_url($cat['slug'])) ?>"
      >
        View All &rarr;
      </a>

    </div>



    <!-- =========================
         BIG CARD
         SAME DESIGN AS TOP HERO
         ========================= -->

    <section class="hero-section wrap">

      <article class="hero-card">


        <!-- BIG CARD IMAGE -->

        <a 
          class="hero-thumb" 
          href="<?= e_attr(blog_url($categoryHero['slug'])) ?>"
        >

          <?php 
          $categoryImage = featured_image($categoryHero); 

          if ($categoryImage): 
          ?>

            <img 
              src="<?= e_attr($categoryImage) ?>" 
              alt="<?= e_attr(
                $categoryHero['featuredImageAlt'] 
                ?? $categoryHero['title']
              ) ?>" 
              width="900" 
              height="500"
            >

          <?php else: ?>

            <span 
              class="post-card-placeholder" 
              aria-hidden="true"
            >
              <?= e(mb_substr(site_name(), 0, 1)) ?>
            </span>

          <?php endif; ?>

        </a>



        <!-- BIG CARD CONTENT -->

        <div class="hero-body">


          <?php if (!empty($categoryHero['category'])): ?>

            <a 
              class="post-card-category" 
              href="<?= e_attr(
                category_url(
                  $categoryHero['category']['slug']
                )
              ) ?>"
            >
              <?= e(
                $categoryHero['category']['name']
              ) ?>
            </a>

          <?php endif; ?>


          <h1>

            <a 
              href="<?= e_attr(
                blog_url(
                  $categoryHero['slug']
                )
              ) ?>"
            >
              <?= e($categoryHero['title']) ?>
            </a>

          </h1>


          <p>
            <?= e(
              excerpt_text(
                $categoryHero,
                200
              )
            ) ?>
          </p>


          <div class="post-card-meta">

            <span>
              <?= e(
                $categoryHero['authorName'] 
                ?? site_name()
              ) ?>
            </span>

            <span aria-hidden="true">
              &middot;
            </span>

            <time>

              <?= e(
                format_date(
                  $categoryHero['publishedAt'] 
                  ?? $categoryHero['createdAt'] 
                  ?? null
                )
              ) ?>

            </time>

          </div>


          <a 
            class="read-story" 
            href="<?= e_attr(
              blog_url(
                $categoryHero['slug']
              )
            ) ?>"
          >

            Read the story

            <span aria-hidden="true">
              ↗
            </span>

          </a>


        </div>

      </article>

    </section>



    <!-- =========================
         3 SMALL CARDS
         SAME EXISTING DESIGN
         ========================= -->

    <?php if ($categoryRest): ?>

      <div class="post-grid post-grid--3">

        <?php foreach ($categoryRest as $blog): ?>

          <?php 
          render_blog_card(
            $blog, 
            'compact'
          ); 
          ?>

        <?php endforeach; ?>

      </div>

    <?php endif; ?>


  </section>


  <?php endforeach; ?>


<?php endif; ?> 


<?php require __DIR__ . '/includes/footer.php'; ?>

