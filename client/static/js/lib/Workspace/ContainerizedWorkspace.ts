/// <reference path='./index.ts'/>

class ContainerizedWorkspace<W extends ContainerizedWorkspace<W>> extends Workspace<W>
{
    public Container : HTMLElement;

    public constructor(container = document.body)
    {
        super();
        this.Container = container;
    }
}