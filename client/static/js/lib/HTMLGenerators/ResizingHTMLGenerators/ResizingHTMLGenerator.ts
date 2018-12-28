/// <reference path='../HTMLGenerator.ts'/>
abstract class ResizingHTMLGenerator<T extends HTMLElement> extends HTMLGenerator<T>
{
    public abstract Resize();
 
    protected Generate()
    {
        let element = super.Generate();
        window.addEventListener("resize", this.Resize.bind(this));

        return element;
    }
}