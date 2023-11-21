class CanvasRenderingContext2DUtils {
    public static getFontSize(context: CanvasRenderingContext2D): number {
        let nums = /\d+(?=px)/i.exec(context.font);
        if (nums !== null && nums.length >= 1) {
            return Number(nums[0]);
        } else {
            return 0;
        }
    }
}

export { CanvasRenderingContext2DUtils };