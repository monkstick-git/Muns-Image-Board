<div id="top"></div>

<!-- PROJECT SHIELDS -->

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

<!-- PROJECT LOGO -->
<br />
<div align="center">

  <h3 align="center">Muns Image Board</h3>

  <p align="center">
    A simple image hosting platform
    <br />
    <a href="FILL ME IN"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://📷.foo.wales">View Demo</a>
    ·
    <a href="https://github.com/monkstick-git/Muns-Image-Board/issues">Report Bug</a>
    ·
    <a href="https://github.com/monkstick-git/Muns-Image-Board/issues">Request Feature</a>
  </p>
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->

## About The Project

There's plenty of places to host images on the internet, but they're not all the same. Some, will allow you to upload for free - but hurt the image quality (Facebook, Google, Instagram, etc). Others, will charge you for your images. Some will be riddled with Ads, and some (If not all) will spy on you.

Because of this - I decided to create a simple, self hosted image hosting platform that is built from the ground up for a containerised environment.

Features:

- Easy to use API so tools such as ShareX can use your own platform as a drop in replacement to upload to
- No Ads, no spying, no tracking. 100% Open Source. What you see is what you get.
- Automatic caching of images and queries to reduce load times using Redis and fully CDN compatible
- Container first approach to ensure the most efficient use of resources. No uploads are saved to disk for easy horizontal scaling.

### Built With

- [bootstrap 4](https://getbootstrap.com)
- [Docker](https://docs.docker.com/get-docker/)
- [PHP 8](https://www.php.net/)
- [MariaDB](https://mariadb.org/)
- [Redis](https://redis.io/)
- [NesCafe](https://www.nestle.co.uk/en-gb/brands/coffee-nescafe)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- GETTING STARTED -->

## Quick Start

```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
git clone git@github.com:monkstick-git/Muns-Image-Board.git
mkdir mysql-data
cd ./src
docker run --rm --interactive --tty --volume $PWD:/app composer install
docker-compose up -d
```

Goto site:port/system/install.php to populate the database and create the schemas etc.


### Prerequisites

- docker + docker-compose
- git

## Roadmap

- [ ] Add Changelog
- [ ] Upgrader Scripts for schema changes
- [ ] New Schema for System information ( like version, total images, space etc )
- [ ] Add Support for video playback
- [ ] Add Support for URL shortening
- [ ] Add Support for General File Hosting
- [ ] Plugin Support
- [ ] Password Protect Images
- [ ] Custom Gallaries
- [ ] More Blob Stores
  - [ ] S3
  - [ ] NextCloud (WebDav)
  - [ ] Disk (Local)

See the [open issues](https://github.com/monkstick-git/Muns-Image-Board/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- CONTRIBUTING -->

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- LICENSE -->

## License

Distributed under the MIT License. See `LICENSE` for more information.

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- CONTACT -->

## Contact

Kieron - [LinkedIn](https://www.linkedin.com/in/kieron-davies-882107169/)

Project Link: [https://github.com/monkstick-git/Muns-Image-Board/](https://github.com/monkstick-git/Muns-Image-Board/)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- ACKNOWLEDGMENTS -->

## Acknowledgments

Use this space to list resources you find helpful and would like to give credit to. I've included a few of my favorites to kick things off!

- [Readme Template](https://github.com/othneildrew/Best-README-Template)
- [Choose an Open Source License](https://choosealicense.com)
- [GitHub Emoji Cheat Sheet](https://www.webpagefx.com/tools/emoji-cheat-sheet)
- [Malven's Flexbox Cheatsheet](https://flexbox.malven.co/)
- [Malven's Grid Cheatsheet](https://grid.malven.co/)
- [Img Shields](https://shields.io)
- [GitHub Pages](https://pages.github.com)
- [Font Awesome](https://fontawesome.com)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[contributors-shield]: https://img.shields.io/github/contributors/monkstick-git/Muns-Image-Board.svg?style=for-the-badge
[contributors-url]: https://github.com/monkstick-git/Muns-Image-Board/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/monkstick-git/Muns-Image-Board.svg?style=for-the-badge
[forks-url]: https://github.com/monkstick-git/Muns-Image-Board/network/members
[stars-shield]: https://img.shields.io/github/stars/monkstick-git/Muns-Image-Board.svg?style=for-the-badge
[stars-url]: https://github.com/monkstick-git/Muns-Image-Board/stargazers
[issues-shield]: https://img.shields.io/github/issues/monkstick-git/Muns-Image-Board.svg?style=for-the-badge
[issues-url]: https://github.com/monkstick-git/Muns-Image-Board/issues
[license-shield]: https://img.shields.io/github/license/monkstick-git/Muns-Image-Board.svg?style=for-the-badge
[license-url]: https://github.com/monkstick-git/Muns-Image-Board/blob/master/LICENSE
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://www.linkedin.com/in/kieron-davies-882107169/
[product-screenshot]: images/screenshot.png
