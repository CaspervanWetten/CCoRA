abstract class GraphModellerMenu
{
    protected Modeller : GraphModeller;
    protected Element  : HTMLElement | undefined;
    public constructor(m : GraphModeller)
    {
        this.Modeller = m;
        this.Element = undefined;
    }

    public Show()
    {
        this.Element = this.GetElement();
        let parent   = this.GetParent();
        parent.appendChild(this.Element);
    }

    public Remove()
    {
        if(this.Element != null) {
            this.Element.remove();
        }
    }

    protected abstract GetElement();

    protected abstract GetParent();

    protected Hide() {
        this.Modeller.HideMenu();
    }
    
    public abstract Focus();
}