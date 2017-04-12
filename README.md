# HowLinks

A PhP and Javascript graphical explorer for RDF step-by-step instructions in the [PROHOW](http://w3id.org/prohow#) format and their related DBpedia pages.

![Screenshot](https://github.com/paolo7/howlinks/blob/master/HowLinksScreenshot.jpg)

## Demo

A live demo of this application is available at this address:
* [http://paolopareti.uk/howlinks/index.php](http://paolopareti.uk/howlinks/index.php)

This demo uses a live triplestore hosted at Dydra:
* [http://dydra.com/paolo-pareti/knowhow6/](http://dydra.com/paolo-pareti/knowhow6/)

And it servers a subset of the Know How Dataset which includes instructions from [wikiHow](http://www.wikihow.com/):
* [https://github.com/paolo7/KnowHowDataset](https://github.com/paolo7/KnowHowDataset)

## Installation

1. Make sure you have a web server running PhP.
2. Download all the files in this repository into your web server.
3. Open file `sparql.php`
4. Change the URL in this line:
    ```
    $db1 = sparql_connect( "http://dydra.com/paolo-pareti/knowhow6/sparql" );
    ```
    to the URL of a SPARQL endpoint containing the PROHOW instructional data that you want to visualise.
5. Optionally, also change the URL in this line:
    ```
    $db2 = sparql_connect( "https://dbpedia.org/sparql" );
    ```
    to the URL of a SPARQL endpoint containing DBpedia data.
6. You application should now be ready to be run in a browser by requesting the `index.php` file.

### References

HowLinks has been developed in collaboration with the [National Informatics Institute of Japan](http://ri-www.nii.ac.jp/HowLinks/index.html) as part of the [The Web of Know-How](http://homepages.inf.ed.ac.uk/s1054760/prohow/index.htm) project.

To cite this application, refer to the following publication:
* Paolo Pareti, Benoit Testu, Ryutaro Ichise, Ewan Klein and Adam Barker. Integrating Know-How into the Linked Data Cloud. Knowledge Engineering and Knowledge Management, volume 8876 of Lecture Notes in Computer Science, pages 385-396. Springer International Publishing (2014) 
