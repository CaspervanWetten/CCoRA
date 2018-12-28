class SetWarningStateStyle extends StyleManagerAction
{
    public Invoke(context : CanvasRenderingContext2D)
    {
        context.fillStyle = "white";
        context.strokeStyle = this.Orange;
        context.lineWidth = 15;
    }
}