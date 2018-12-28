/// <reference path='./index.ts'/>

class ParagraphBuilder extends HTMLGenerator<HTMLParagraphElement>
{
    protected Items : Node[];
    
    public constructor()
    {
        super();
        this.ClassPrefix = "PARAGRAPH_BUILDER";
        this.Items = [];
    }

    public Add(a : string | Node)
    {
        if(typeof a == "string") {
            a = document.createTextNode(a);
        }
        this.Items.push(a);
    }

    public Clear()
    {
        this.Items = [];
        this.Element = null;
    }

    public GenerateElement()
    {
        let p = document.createElement("p");

        for(let i = 0; i < this.Items.length; i++) {
            p.appendChild(this.Items[i]);
        }

        return p;
    }
}