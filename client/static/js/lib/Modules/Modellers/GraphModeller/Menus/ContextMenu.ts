/// <reference path='./index.ts'/>

class GraphModellerContextMenu extends GraphModellerMenu
{
    public Position : Point;
    protected RelativePosition : Point;
    
    public constructor(modeler : GraphModeller, position : Point, relpos?: Point)
    {
        super(modeler);
        this.Position = position;
        this.RelativePosition = relpos;
    }

    public Focus()
    {
        if(this.Element != null) this.Element.focus();
    }

    protected GetElement()
    {
        let modeler = this.Modeller;
        let menu = new ContextMenu(this.Position.X, this.Position.Y);
        menu.Add("Add State", ()=>{ modeler.AddState(this.RelativePosition); });
        if(modeler.SelectedId != null) {
            let graph = Store.GetInstance().GetGraph();
            if(graph.ContainsState(modeler.SelectedId)) {
                menu.Add("Set Initial", modeler.SetInitial.bind(modeler))
            }
            menu.Add("Edit Element", modeler.EditElementMenu.bind(modeler));
            menu.Add("Remove Element", modeler.RemoveElement.bind(modeler));
            let settings = Settings.GetInstance();
            if(settings.GetDebug()) {
                menu.Add(modeler.SelectedId.toString(), ()=>{
                    console.log(modeler.Feedback.SpecificItems.get(modeler.SelectedId).GetCodes().sort());
                });
            }
        }

        return menu.Render();
    }

    protected GetParent()
    {
        return this.Modeller.GetDrawer().GetElement().parentElement;
    }
}