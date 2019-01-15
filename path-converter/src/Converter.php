<?php

namespace MatthiasMullie\PathConverter;

/**
 * Convert paths relative from 1 file to another.
 *
 * E.g.
 *     ../../images/icon.jpg relative to /css/imports/icons.css
 * becomes
 *     ../images/icon.jpg relative to /css/minified.css
 *
 * Please report bugs on https://github.com/matthiasmullie/path-converter/issues
 *
 * @author Matthias Mullie <pathconverter@mullie.eu>
 * @copyright Copyright (c) 2015, Matthias Mullie. All rights reserved
 * @license MIT License
 */
class Converter implements ConverterInterface
{
    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $to;

    /**
     * @param string $from The original base path (directory, not file!)
     * @param string $to   The new base path (directory, not file!)
     */
    public function __construct($from, $to)
    {
        $shared = $this->shared($from, $to);
        if ($shared === '') {
            // when both paths have nothing in common, one of them is probably
            // absolute while the other is relative
            $cwd = getcwd();
            $from = strpos($from, $cwd) === 0 ? $from : $cwd.'/'.$from;
            $to = strpos($to, $cwd) === 0 ? $to : $cwd.'/'.$to;

            // or traveling the tree via `..`
            // attempt to resolve path, or assume it's fine if it doesn't exist
            $from = @realpath($from) ?: $from;
            $to = @realpath($to) ?: $to;
        }

        $from = $this->dirname($from);
        $to = $this->dirname($to);

        $from = $this->normalize($from);
        $to = $this->normalize($to);

        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Normalize path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function normalize($path)
    {
        // deal with different operating systems' directory structure
        $path = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $path), '/');

        /*
         * Example:
         *     /home/forkcms/frontend/cache/compiled_templates/../../core/layout/css/../images/img.gif
         * to
         *     /home/forkcms/frontend/core/layout/images/img.gif
         */
        do {
            $path = preg_replace('/[^\/]+(?<!\.\.)\/\.\.\//', '', $path, -1, $count);
        } while ($count);

        return $path;
    }

    /**
     * Figure out the shared path of 2 locations.
     *
     * Example:
     *     /home/forkcms/frontend/core/layout/images/img.gif
     * and
     *     /home/forkcms/frontend/cache/minified_css
     * share
     *     /home/forkcms/frontend
     *
     * @param string $path1
     * @param string $path2
     *
     * @return string
     */
    protected function shared($path1, $path2)
    {
        // $path could theoretically be empty (e.g. no path is given), in which
        // case it shouldn't expand to array(''), which would compare to one's
        // root /
        $path1 = $path1 ? explode('/', $path1) : array();
        $path2 = $path2 ? explode('/', $path2) : array();

        $shared = array();

        // compare paths & strip identical ancestors
        foreach ($path1 as $i => $chunk) {
            if (isset($path2[$i]) && $path1[$i] == $path2[$i]) {
                $shared[] = $chunk;
            } else {
                break;
            }
        }

        return implode('/', $shared);
    }

    /**
     * Convert paths relative from 1 file to another.
     *
     * E.g.
     *     ../images/img.gif relative to /home/forkcms/frontend/core/layout/css
     * should become:
     *     ../../core/layout/images/img.gif relative to
     *     /home/forkcms/frontend/cache/minified_css
     *
     * @param string $path The relative path that needs to be converted
     *
     * @return string The new relative path
     */
    public function convert($path)
    {
        // quit early if conversion makes no sense
        if ($this->from === $this->to) {
            return $path;
        }

        //$path = $this->normalize($path);
        // if we're not dealing with a relative path, just return absolute
        if (strpos($path, '/') === 0) {
            return $path;
        }

        $dr_content_path = explode('wp-content', $this->from."/myfile.css");
        if(sizeof($dr_content_path)>1){
            $dr_to_path = WP_CONTENT_URL . $dr_content_path[1];
            return $this->rel2abs( $path, $dr_to_path );
        }

        // normalize paths
        $path = $this->normalize($this->from.'/'.$path);

        // strip shared ancestor paths
        $shared = $this->shared($path, $this->to);
        $path = mb_substr($path, mb_strlen($shared));
        $to = mb_substr($this->to, mb_strlen($shared));

        // add .. for every directory that needs to be traversed to new path
        $to = str_repeat('../', mb_substr_count($to, '/'));

        return $to.ltrim($path, '/');
    }


    public function rel2abs( $rel, $base )
    {
        /* return if already absolute URL */
        if( parse_url($rel, PHP_URL_SCHEME) != '' )
            return( $rel );
    
        /* queries and anchors */
        if( $rel[0]=='#' || $rel[0]=='?' )
            return( $base.$rel );
    
        /* parse base URL and convert to local variables:
           $scheme, $host, $path */
        extract( parse_url($base) );
    
        /* remove non-directory element from path */
        $path = preg_replace( '#/[^/]*$#', '', $path );
    
        /* destroy path if relative url points to root */
        if( $rel[0] == '/' )
            $path = '';
    
        /* dirty absolute URL */
        $abs = '';
    
        /* do we have a user in our URL? */
        if( isset($user) )
        {
            $abs.= $user;
    
            /* password too? */
            if( isset($pass) )
                $abs.= ':'.$pass;
    
            $abs.= '@';
        }
    
        $abs.= $host;
    
        /* did somebody sneak in a port? */
        if( isset($port) )
            $abs.= ':'.$port;
    
        $abs.=$path.'/'.$rel;
    
        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for( $n=1; $n>0; $abs=preg_replace( $re, '/', $abs, -1, $n ) ) {}
    
        /* absolute URL is ready! */
        return( $scheme.'://'.$abs );
    }
    

    /**
     * Attempt to get the directory name from a path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function dirname($path)
    {
        if (@is_file($path)) {
            return dirname($path);
        }

        if (@is_dir($path)) {
            return rtrim($path, '/');
        }

        // no known file/dir, start making assumptions

        // ends in / = dir
        if (mb_substr($path, -1) === '/') {
            return rtrim($path, '/');
        }

        // has a dot in the name, likely a file
        if (preg_match('/.*\..*$/', basename($path)) !== 0) {
            return dirname($path);
        }

        // you're on your own here!
        return $path;
    }
}
