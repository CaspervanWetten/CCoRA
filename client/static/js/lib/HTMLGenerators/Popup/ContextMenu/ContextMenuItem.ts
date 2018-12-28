class ContextMenuItem extends HTMLGenerator<HTMLElement>
{
    Text : string;
    Action : EventListenerOrEventListenerObject;

    public constructor(text : string, action? : EventListenerOrEventListenerObject)
    {
        super();
        this.Text = text;
        if(action)
            this.Action = action;

        this.ClassPrefix = "__CONTEXT_MENU_ITEM__";
    }

    protected GenerateElement()
    {
        let item = document.createElement('div');
        item.appendChild(document.createTextNode(this.Text));
        item.addEventListener('click', this.Action);
        return item;
    }
    
    public getText()
    {
        return this.Text;
    }

    public setText(text : string)
    {
        this.Text = text;
    }

    public getAction()
    {
        return this.Action;
    }

    public setAction(action : EventListenerOrEventListenerObject)
    {
        this.Action = action;
    }
}