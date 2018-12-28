/// <reference path='./index.ts'/>

/// <reference path='../../../../Drawings/index.ts'/>

abstract class EdgeDrawing extends GraphElementDrawing
{
    protected Edge          : Edge;
    protected From          : StateDrawing;
    
    public constructor(drawer : GraphDrawer, edge : Edge, from : StateDrawing, trans : Transition)
    {
        super(drawer);
        this.Edge = edge;
        this.From = from;
    }

    protected abstract DrawText(context : CanvasRenderingContext2D);

    //#region Getters and Setters
    public GetEdge() {
        return this.Edge;
    }
    public GetFromDrawing() {
        return this.From;
    }
    //#endregion
}