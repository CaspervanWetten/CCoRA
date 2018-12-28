/// <reference path='./index.ts'/>

class ModellerWorkspace extends ContainerizedWorkspace<ModellerWorkspace>
{
    public constructor(container : HTMLElement)
    {
        super(container);
        this.SetAction(new InitModeller(this));
    }
}