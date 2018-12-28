class SVGParser 
{
    /**
     * Creates a new svg element from a given svg-string
     * @param svgString The string containing the SVG element
     */
    public static ParseSvg(svgString : string) : HTMLElement
    {
        let parser = new DOMParser();
        let img = parser.parseFromString(svgString, "image/svg+xml").documentElement;
        img.removeAttribute("width");
        img.removeAttribute("height");
        return img;
    }
}