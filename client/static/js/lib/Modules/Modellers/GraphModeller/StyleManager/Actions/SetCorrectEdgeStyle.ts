class SetCorrectEdgeStyle extends StyleManagerAction
{
    public Invoke(context : CanvasRenderingContext2D)
    {
        context.strokeStyle = this.Green;
        context.fillStyle   = "white";
        context.lineWidth   = 10;
    }
}