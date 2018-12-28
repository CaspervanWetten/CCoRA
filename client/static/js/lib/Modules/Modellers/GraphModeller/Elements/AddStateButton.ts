/// <reference path='../../../../HTMLGenerators/index.ts'/>

class AddStateButton extends HTMLGenerator<HTMLButtonElement>
{
    public constructor()
    {
        super();
        this.ClassPrefix = "ADD_STATE_BUTTON";
        this.ElementId = "addState";
    }

    protected GenerateElement()
    {
        let button = document.createElement("button");
        button.appendChild(document.createTextNode("Add State"));
        this.AddClassname(button, "modeler add");
        return button;
    }
}